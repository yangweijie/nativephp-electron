<?php

namespace yangweijie\thinkElectron\updater;


use InvalidArgumentException;
use think\App;

class UpdaterManager
{
    /**
     * The application instance.
     */
    protected App $app;

    /**
     * The array of resolved updater providers.
     *
     * @var array
     */
    protected array $providers = [];

    /**
     * Create a new Updater manager instance.
     *
     * @return void
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Get a updater provider instance by name, wrapped in a repository.
     *
     * @param string|null $name
     */
    public function provider(string $name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->providers[$name] ??= $this->resolve($name);
    }

    /**
     * Get a updater provider instance.
     *
     * @param string|null $driver
     */
    public function driver(string $driver = null)
    {
        return $this->store($driver);
    }

    /**
     * Resolve the given store.
     *
     * @param string $name
     * @return mixed
     */
    public function resolve(string $name): mixed
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("NativePHP updater provider [{$name}] is not defined.");
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Get the updater provider configuration.
     *
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name): ?array
    {
        if ($name !== 'null') {
            return $this->app['config']["nativephp.updater.providers.{$name}"];
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the default updater driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['nativephp.updater.default'];
    }

    /**
     * Set the default updater driver name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultDriver(string $name): void
    {
        $this->app['config']['nativephp.updater.default'] = $name;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param App $app
     * @return $this
     */
    public function setApplication(App $app): static
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Create an instance of the spaces updater driver.
     *
     */
    protected function createSpacesDriver(array $config): SpacesProvider
    {
        return new SpacesProvider($config);
    }

    /**
     * Create an instance of the spaces updater driver.
     *
     */
    protected function createS3Driver(array $config): S3Provider
    {
        return new S3Provider($config);
    }

    /**
     * Create an instance of the GitHub updater driver.
     *
     */
    protected function createGitHubDriver(array $config): GitHubProvider
    {
        return new GitHubProvider($config);
    }

    /**
     * Dynamically call the default updater instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->provider()->$method(...$parameters);
    }
}
