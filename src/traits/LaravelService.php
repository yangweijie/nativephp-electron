<?php

namespace native\thinkElectron\traits;

use think\App;
use think\facade\Filesystem;

/**
 * Laravel ServiceProvider 兼容 Trait
 * 
 * 此 Trait 提供了与 Laravel ServiceProvider 类似的功能，
 * 特别是资源发布相关的方法
 */
trait LaravelService
{
    /**
     * 发布的资源集合
     *
     * @var array
     */
    protected static $publishes = [];

    /**
     * 发布组
     *
     * @var array
     */
    protected static $publishGroups = [];

    /**
     * 注册要发布的资源
     *
     * @param  array  $paths 路径数组，键为源路径，值为目标路径
     * @param  string|array|null  $groups 分组名称
     * @return void
     */
    protected function publishes(array $paths, $groups = null)
    {
        $class = static::class;

        if (! array_key_exists($class, static::$publishes)) {
            static::$publishes[$class] = [];
        }

        static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

        foreach ((array) $groups as $group) {
            if (! array_key_exists($group, static::$publishGroups)) {
                static::$publishGroups[$group] = [];
            }

            static::$publishGroups[$group] = array_merge(
                static::$publishGroups[$group], $paths
            );
        }
    }

    /**
     * 注册要发布的配置文件
     *
     * @param  array  $paths 路径数组
     * @param  string|null  $group 分组名称
     * @return void
     */
    protected function publishesConfig(array $paths, $group = null)
    {
        $this->publishes($paths, $group ?? 'config');
    }

    /**
     * 注册要发布的迁移文件
     *
     * @param  array  $paths 路径数组
     * @param  string|null  $group 分组名称
     * @return void
     */
    protected function publishesMigrations(array $paths, $group = null)
    {
        $this->publishes($paths, $group ?? 'migrations');
    }

    /**
     * 注册要发布的视图文件
     *
     * @param  array  $paths 路径数组
     * @param  string|null  $group 分组名称
     * @return void
     */
    protected function publishesViews(array $paths, $group = null)
    {
        $this->publishes($paths, $group ?? 'views');
    }

    /**
     * 注册要发布的资源文件
     *
     * @param  array  $paths 路径数组
     * @param  string|null  $group 分组名称
     * @return void
     */
    protected function publishesAssets(array $paths, $group = null)
    {
        $this->publishes($paths, $group ?? 'assets');
    }

    /**
     * 获取指定服务提供者的发布资源
     *
     * @param  string|null  $provider 服务提供者类名
     * @param  string|null  $group 分组名称
     * @return array
     */
    public static function pathsToPublish($provider = null, $group = null)
    {
        if ($provider && $group) {
            if (empty(static::$publishGroups[$group])) {
                return [];
            }

            return array_intersect_key(static::$publishGroups[$group], static::$publishes[$provider] ?? []);
        }

        if ($provider) {
            return static::$publishes[$provider] ?? [];
        }

        if ($group) {
            return static::$publishGroups[$group] ?? [];
        }

        $publishes = [];

        foreach (static::$publishes as $class => $paths) {
            $publishes = array_merge($publishes, $paths);
        }

        return $publishes;
    }

    /**
     * 执行资源发布
     *
     * @param  string|null  $provider 服务提供者类名
     * @param  string|null  $group 分组名称
     * @param  bool  $force 是否强制覆盖
     * @return void
     */
    public static function publishResources($provider = null, $group = null, $force = false)
    {
        $paths = static::pathsToPublish($provider, $group);

        foreach ($paths as $from => $to) {
            if (is_dir($from)) {
                static::publishDirectory($from, $to, $force);
            } elseif (is_file($from)) {
                static::publishFile($from, $to, $force);
            }
        }
    }

    /**
     * 发布目录
     *
     * @param  string  $from 源目录
     * @param  string  $to 目标目录
     * @param  bool  $force 是否强制覆盖
     * @return void
     */
    protected static function publishDirectory($from, $to, $force = false)
    {
        if (! is_dir($from)) {
            return;
        }

        if (! is_dir($to)) {
            mkdir($to, 0755, true);
        }

        $files = scandir($from);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fromPath = $from . DIRECTORY_SEPARATOR . $file;
            $toPath = $to . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fromPath)) {
                static::publishDirectory($fromPath, $toPath, $force);
            } else {
                static::publishFile($fromPath, $toPath, $force);
            }
        }
    }

    /**
     * 发布文件
     *
     * @param  string  $from 源文件
     * @param  string  $to 目标文件
     * @param  bool  $force 是否强制覆盖
     * @return void
     */
    protected static function publishFile($from, $to, $force = false)
    {
        if (! is_file($from)) {
            return;
        }

        if (! is_dir(dirname($to))) {
            mkdir(dirname($to), 0755, true);
        }

        if (! $force && is_file($to)) {
            return;
        }

        copy($from, $to);
    }
}