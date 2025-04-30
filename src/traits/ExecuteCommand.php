<?php

namespace yangweijie\thinkElectron\traits;

use Illuminate\Support\Facades\Process;

use yangweijie\thinkElectron\concerns\LocatesPhpBinary;
use function Laravel\Prompts\note;

trait ExecuteCommand
{
    use LocatesPhpBinary;

    protected function executeCommand(
        string $command,
        bool $skip_queue = false,
        string $type = 'install',
        bool $withoutInteraction = false
    ): void {
        $envs = [
            'install' => [
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => root_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => root_path($this->binaryPackageDirectory().'cacert.pem'),
            ],
            'serve' => [
                'APP_PATH' => root_path(),
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => root_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => root_path($this->binaryPackageDirectory().'cacert.pem'),
                'NATIVE_PHP_SKIP_QUEUE' => $skip_queue,
                'NATIVEPHP_BUILDING' => false,
            ],
        ];

        note('Fetching latest dependencies…');

        Process::path(__DIR__.'/../../resources/js/')
            ->env($envs[$type])
            ->forever()
            ->tty(! $withoutInteraction && PHP_OS_FAMILY != 'Windows')
            ->run($command, function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
    }

    protected function getCommandArrays(string $type = 'install'): array
    {
        $commands = [
            'install' => [
                'npm' => 'npm install',
                'yarn' => 'yarn',
                'pnpm' => 'pnpm install',
            ],
            'dev' => [
                'npm' => 'npm run dev',
                'yarn' => 'yarn dev',
                'pnpm' => 'pnpm run dev',
            ],
        ];

        return $commands[$type];
    }
}
