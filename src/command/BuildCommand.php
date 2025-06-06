<?php

namespace native\thinkElectron\command;

use think\console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use native\thinkElectron\concerns\LocatesPhpBinary;
use native\thinkElectron\facade\Updater;
use native\thinkElectron\traits\InstallsAppIcon;
use native\thinkElectron\traits\OsAndArch;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;

class BuildCommand extends Command
{
    use LaravelCommand;
    use InstallsAppIcon;
    use LocatesPhpBinary;
    use OsAndArch;

    public function __construct(){
        $this->signature = 'native:build
        {os? : The operating system to build for (all, linux, mac, win)}
        {arch? : The Processor Architecture to build for (x64, x86, arm64)}
        {--publish : to publish the app}';
        parent::__construct();
    }

    protected array $availableOs = ['win', 'linux', 'mac', 'all'];

    public function handle(): void
    {
        $this->info('Build NativePHP app…');

        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->run('npm update', function (string $type, string $output) {
                echo $output;
            });

        Process::path(base_path())
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        $os = $this->selectOs($this->argument('os'));

        $this->installIcon();

        $buildCommand = 'build';
        if ($os != 'all') {
            $arch = $this->selectArchitectureForOs($os, $this->argument('arch'));

            $os .= $arch != 'all' ? "-{$arch}" : '';

            // Should we publish?
            if ($publish = $this->option('publish')) {
                $buildCommand = 'publish';
            }
        }

        $this->info((($publish ?? false) ? 'Publishing' : 'Building')." for {$os}");

        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->tty(PHP_OS_FAMILY != 'Windows' && ! $this->option('no-interaction'))
            ->run("npm run {$buildCommand}:{$os}", function (string $type, string $output) {
                echo $output;
            });
    }

    protected function getEnvironmentVariables(): array
    {
        return array_merge(
            [
                'APP_PATH' => base_path(),
                'APP_URL' => config('app.url'),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => base_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($this->binaryPackageDirectory().'cacert.pem'),
                'NATIVEPHP_APP_NAME' => config('app.name'),
                'NATIVEPHP_APP_ID' => config('nativephp.app_id'),
                'NATIVEPHP_APP_VERSION' => config('nativephp.version'),
                'NATIVEPHP_APP_FILENAME' => Str::slug(config('app.name')),
                'NATIVEPHP_APP_AUTHOR' => config('nativephp.author'),
                'NATIVEPHP_UPDATER_CONFIG' => json_encode(Updater::builderOptions()),
                'NATIVEPHP_DEEPLINK_SCHEME' => config('nativephp.deeplink_scheme'),
            ],
            Updater::environmentVariables(),
        );
    }
}
