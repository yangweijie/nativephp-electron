<?php

namespace native\thinkElectron\facade;

use Closure;
use native\thinkElectron\ProcessWrapper;
use think\Facade;
use Traversable;

/**
 * @method static ProcessWrapper command(array|string $command)
 * @method static ProcessWrapper path(string $path)
 * @method static ProcessWrapper timeout(int $timeout)
 * @method static ProcessWrapper idleTimeout(int $timeout)
 * @method static ProcessWrapper forever()
 * @method static ProcessWrapper env(array $environment)
 * @method static ProcessWrapper input(Traversable|resource|string|int|float|bool|null $input)
 * @method static ProcessWrapper quietly()
 * @method static ProcessWrapper tty(bool $tty = true)
 * @method static ProcessWrapper options(array $options)
 * @method static ProcessWrapper run(array|string|null $command = null, callable|null $output = null)
 * @method static ProcessWrapper start(array|string|null $command = null, callable|null $output = null)
 * @method static ProcessWrapper withFakeHandlers(array $fakeHandlers)
 * @method static ProcessWrapper|mixed when(Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static ProcessWrapper|mixed unless(Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 */

class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ProcessWrapper::class;
    }

    /**
     * Dynamically proxy methods to a new pending process instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return (new ProcessWrapper())->{$method}(...$parameters);
    }
}