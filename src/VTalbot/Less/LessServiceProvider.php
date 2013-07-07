<?php

namespace VTalbot\Less;

use \Config;
use \DateInterval;
use \File;
use \Less;
use \Response;
use \Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;
use VTalbot\Less\Compilers\LessCompiler;

class LessServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('vtalbot/less');
        $this->registerRoutes();
        $this->registerEngineResolver();
        $this->registerLessFinder();
        $this->registerEnvironment();
    }

    /**
     * Register routes to catch LESS request.
     *
     * @return void
     */
    public function registerRoutes()
    {
        $prefixes = Config::get('less::prefix');

        if ( ! is_array($prefixes))
        {
            $prefixes = array($prefixes);
        }

        foreach (Config::get('less::routes') as $routes)
        {
            foreach (Config::get('less::extensions') as $ext)
            {
                foreach ($prefixes as $prefix)
                {
                    Route::get($prefix.$routes.'{file}.'.$ext, function($file) use ($routes)
                    {
                        $less = Less::make($routes.$file);
                        $response = Response::make($less, 200, array('Content-Type' => 'text/css'));
                        $response->setCache(array('public' => true));

                        if ( ! is_null(Config::get('less::expires')))
                        {
                            $date = date_create();
                            $date->add(new DateInterval('PT'.Config::get('less::expires').'M'));
                            $response->setExpires($date);
                        }

                        return $response;
                    })->where('file', '.*');
                }
            }
        }
    }

    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        list($me, $app) = array($this, $this->app);

        $app['less.engine.resolver'] = $app->share(function($app) use ($me)
            {
                $resolver = new EngineResolver;
                $me->registerLessEngine($resolver);

                return $resolver;
            });
    }

    /**
     * Register the LESS engine implementation.
     *
     * @param  Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerLessEngine($resolver)
    {
        $app = $this->app;

        $resolver->register('less', function() use ($app)
            {
                $cache = storage_path().'/less';

                if ( ! File::isDirectory($cache))
                {
                    File::makeDirectory($cache);
                }

                $compiler = new LessCompiler(app('files'), $cache);

                return new CompilerEngine($compiler, app('files'));
            });
    }

    /**
     * Register the LESS finder implementation.
     *
     * @return void
     */
    public function registerLessFinder()
    {
        $this->app['less.finder'] = $this->app->share(function($app)
            {
                $paths = Config::get('less::paths');

                foreach ($paths as $key => $path)
                {
                    $paths[$key] = app_path().$path;
                }

                return new FileViewFinder(app('files'), $paths, array('less'));
            });
    }

    /**
     * Register the LESS environment.
     *
     * @return void
     */
    public function registerEnvironment()
    {
        $me = $this;

        $this->app['less'] = $this->app->share(function($app) use ($me)
            {
                $resolver = $app['less.engine.resolver'];
                $finder = $app['less.finder'];
                $events = $app['events'];
                $environment = new Environment($resolver, $finder, $events);
                $environment->setContainer($app);
                $environment->share('app', $app);

                return $environment;
            });
    }

}
