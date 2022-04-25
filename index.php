<?php

ini_set('display_errors', true);

require_once __DIR__ . '/vendor/autoload.php';

use App\Util\FileInfo;
use App\Util\Collection;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\BaseFilter;
use App\Core\Filter\CorsFilter;
use App\Core\Filter\AuthenticationFilter;
use App\Core\Exception\ExceptionHandler;

class Bootstrap
{
    private $di;
    private $timezoneId = 'prc';
    private $configPath = __DIR__ . '/config.php';
    private $routerPath;

    public function __construct()
    {
        $this->di = \App\DI();
    }

    public function init()
    {
        // global exception handler
        ExceptionHandler::register();

        $this->initTimezone();

        $this->initConfig();

        $this->initRequest();

        $this->initResponse();

        $this->initCache();

        $this->initRouter();

        $this->initUtil();

        return $this;
    }

    public function setTimezone(string $timezoneId)
    {
        $this->timezoneId = $timezoneId;
        return $this;
    }

    private function initTimezone()
    {
        date_default_timezone_set($this->timezoneId);
    }

    public function setConfig($path)
    {
        $this->configPath = $path;
        return $this;
    }

    /**
     * all config files must be place at the same dir.
     * env config should name as config-xxx.php, eg. config-development.php
     */
    private function initConfig()
    {
        $configFile = new FileInfo($this->configPath, true);
        $config = require_once $this->configPath;
        $basePath = $config['basePath'];
        // $basePath is from config.php, so require config.php first
        $configEnv = require_once sprintf('%s/%s-%s.%s', $basePath, $configFile->fileNameWithoutExt, $config['env'], $configFile->fileExt);
        $this->di->config = new Collection(array_merge($config, $configEnv));
    }

    private function initRequest()
    {
        $this->di->request = new Request();
    }

    private function initResponse()
    {
        $this->di->response = (new Response())
            ->enableDebug($this->di->config['debug'])
            ->setDebugKey($this->di->config['controller']['response']['structureMap']['debug']);
    }

    private function initCache()
    {
        $this->di->cache = new Predis\Client($this->di->config['datasource']['redis']);
    }

    public function setRouter($path)
    {
        $this->routerPath = $path;
        return $this;
    }

    private function initRouter()
    {
        if (!$this->routerPath) {
            $this->routerPath = $this->di->config['router']['path'];
        }
        $this->di->router = require_once $this->routerPath;
        return $this;
    }

    private function initUtil()
    {
        // faker data util
        $this->di->faker = Faker\Factory::create('en_US');

        // image handle util
        $this->di->image = new Intervention\Image\ImageManager(['driver' => 'imagick']);
    }

    public function doFilterChain(BaseFilter ...$filters)
    {
        foreach ($filters as $filter) {
            if (!$filter->doFilter()) {
                break;
            }
        }
        return $this;
    }

    public function dispatch()
    {
        $this->di->router->dispatch($this->di->request, $this->di->response);
    }
}

$bootstrap = (new Bootstrap())
    // ->setTimezone('prc')
    // ->setConfig(__DIR__ . '/config.php')
    // ->setRouter(__DIR__ . '/router.php')
    ->init();

$bootstrap
    ->doFilterChain(
        new CorsFilter(),
        new AuthenticationFilter()
    )
    ->dispatch();
