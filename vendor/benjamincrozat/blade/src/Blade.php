<?php

namespace BC\Blade;

use Illuminate\View\Factory;
use Illuminate\Events\Dispatcher;
use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

/**
 * @method \Illuminate\Contracts\View\View make($view, $data = [], $mergeData = [])
 * @method \Illuminate\Contracts\View\View with($key, $value = null)
 */
class Blade
{
    /**
     * Array of paths to Blade files.
     *
     * @var array
     */
    protected $viewPaths;

    /**
     * Path to compiled Blade files.
     *
     * @var string
     */
    protected $compiledPath;

    /**
     * @var DispatcherContract
     */
    protected $events;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var EngineResolver
     */
    protected $resolver;

    /**
     * @var CompilerInterface
     */
    protected $bladeCompiler;

    /**
     * @var FileViewFinder
     */
    protected $finder;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * @param string|array             $view_paths
     * @param string                   $compiled_path
     * @param DispatcherContract|null  $events
     * @param Filesystem|null          $files
     * @param EngineResolver|null      $resolver
     * @param ViewFinderInterface|null $finder
     * @param FactoryContract|null     $factory
     */
    public function __construct($view_paths, $compiled_path, DispatcherContract $events = null, ViewFinderInterface $finder = null, FactoryContract $factory = null)
    {
        $this->viewPaths    = (array) $view_paths;
        $this->compiledPath = (string) $compiled_path;
        $this->events       = $events ?: new Dispatcher();

        $this->registerFilesystem()
            ->registerEngineResolver()
            ->registerViewFinder($finder)
            ->registerFactory($factory);
    }

    /**
     * Undefined methods are proxied to the compiler
     * and the view factory for API ease of use.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->bladeCompiler, $name)) {
            return $this->bladeCompiler->{$name}(...$arguments);
        }

        if (method_exists($this->view, $name)) {
            return $this->view->{$name}(...$arguments);
        }
    }

    /**
     * @return self
     */
    protected function registerFilesystem()
    {
        $this->files = new Filesystem();

        return $this;
    }

    /**
     * @return self
     */
    protected function registerEngineResolver()
    {
        $this->resolver = new EngineResolver();

        return $this->registerPhpEngine()
            ->registerBladeEngine();
    }

    /**
     * @return self
     */
    protected function registerPhpEngine()
    {
        $this->resolver->register('php', function () {
            return new PhpEngine();
        });

        return $this;
    }

    /**
     * @return self
     */
    protected function registerBladeEngine()
    {
        $this->bladeCompiler = new BladeCompiler($this->files, $this->compiledPath);

        $this->resolver->register('blade', function () {
            return new CompilerEngine($this->bladeCompiler, $this->files);
        });

        return $this;
    }

    /**
     * @param ViewFinderInterface|null $finder
     *
     * @return self
     */
    protected function registerViewFinder(ViewFinderInterface $finder = null)
    {
        $this->finder = $finder ?: new FileViewFinder($this->files, $this->viewPaths);

        return $this;
    }

    /**
     * @param FactoryContract|null $factory
     *
     * @return self
     */
    protected function registerFactory(FactoryContract $factory = null)
    {
        $this->view = $factory ?: new Factory($this->resolver, $this->finder, $this->events);

        return $this;
    }
}
