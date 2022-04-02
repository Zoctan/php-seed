<?php

ini_set('display_errors', true);

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Collection;
use App\Core\Filter;
use App\Core\Filter\CorsFilter;
use App\Core\Filter\AuthenticationFilter;
use App\Core\Exception\ExceptionHandler;

class Bootstrap
{
    private $di;
    private $timezoneId = 'prc';
    private $configPath = __DIR__ . '/config.php';
    private $routerPath = __DIR__ . '/routes.php';

    public function __construct()
    {
        $this->di = \App\DI();
    }

    public function init()
    {
        $this->initTimezone();

        $this->initConfig();

        $this->initRequest();

        $this->initResponse();

        $this->initCache();

        $this->initRouter();

        // 注册全局异常处理器
        ExceptionHandler::register();

        return $this;
    }

    public function setTimezone(string $timezoneId)
    {
        $this->timezoneId = $timezoneId;
        return $this;
    }

    public function initTimezone()
    {
        date_default_timezone_set($this->timezoneId);
    }

    public function setConfig($path)
    {
        $this->configPath = $path;
        return $this;
    }

    public function initConfig()
    {
        $this->di->config = new Collection(require_once $this->configPath);
    }

    public function setRouter($path)
    {
        $this->routerPath = $path;
        return $this;
    }

    public function initRouter()
    {
        $this->di->router = require_once $this->routerPath;
        return $this;
    }

    public function initUtil()
    {
        // 注册伪造数据工具
        $this->di->faker = Faker\Factory::create('zh_CN');

        // 注册 HTTP 客户端
        $this->di->curl = new GuzzleHttp\Client();

        // 注册图片处理工具
        $this->di->image = new Intervention\Image\ImageManager(['driver' => 'imagick']);
    }

    private function initRequest()
    {
        $this->di->request = new Request();
    }

    private function initResponse()
    {
        $this->di->response = (new Response())
            ->setDebug($this->di->config['app']['debug'])
            ->setDebugKey($this->di->config['app']['response']['structureMap']['debug'])
            ->setResponseType($this->di->config['app']['response']['type']);
    }

    private function initCache()
    {
        $this->di->cache = new Predis\Client($this->di->config['datasource']['redis']);
    }

    public function doFilterChain(Filter ...$filters)
    {
        foreach ($filters as $filter) {
            if (!$filter->doFilter()) {
                break;
            }
        }
        return $this;
    }

    public function start()
    {
        // 路由分发、处理请求、返回响应
        $this->di->router->dispatch($this->di->request);
    }
}

$bootstrap = (new Bootstrap())
    ->init();

$bootstrap
    ->doFilterChain(
        new CorsFilter(),
        new AuthenticationFilter()
    )
    ->start();
