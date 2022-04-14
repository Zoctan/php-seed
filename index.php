<?php

ini_set('display_errors', true);

require_once __DIR__ . '/vendor/autoload.php';

use App\Util\File;
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

    /**
     * all config files must be place at the same dir.
     * env config should name as config-xxx.php, eg. config-development.php
     */
    public function initConfig()
    {
        $config = require_once $this->configPath;
        $basePath = $config['basePath'];
        $configFile = new File($this->configPath, $basePath);
        // $basePath is from config.php, so require config.php first
        $configEnv = require_once sprintf('%s/%s-%s.%s', $basePath, $configFile->fileNameWithoutExt, $config['env'], $configFile->fileExt);
        $this->di->config = new Collection(array_merge($config, $configEnv));
    }

    public function setRouter($path)
    {
        $this->routerPath = $path;
        return $this;
    }

    public function initRouter()
    {
        if (!$this->routerPath) {
            $this->routerPath = $this->di->config['app']['routerPath'];
        }
        $this->di->router = require_once $this->routerPath;
        return $this;
    }

    public function initUtil()
    {
        // faker data util
        $this->di->faker = Faker\Factory::create('zh_CN');

        // http client util
        $this->di->curl = new GuzzleHttp\Client();

        // image handle util
        $this->di->image = new Intervention\Image\ImageManager(['driver' => 'imagick']);
    }

    private function initRequest()
    {
        $this->di->request = new Request();
    }

    private function initResponse()
    {
        $this->di->response = (new Response())
            ->setDebug($this->di->config['debug'])
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

    public function dispatch()
    {
        $this->di->router->dispatch($this->di->request, $this->di->response);
    }
}

$bootstrap = (new Bootstrap())
    ->init();

$bootstrap
    ->doFilterChain(
        new CorsFilter(),
        new AuthenticationFilter()
    )
    ->dispatch();
