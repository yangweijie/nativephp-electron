<?php

namespace native\thinkElectron\command;

use think\console\Command;

use native\thinkElectron\traits\Developer;
use native\thinkElectron\traits\Installer;
use native\thinkElectron\traits\InstallsAppIcon;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;

class DevelopCommand extends Command
{
    use LaravelCommand;
    use Developer, Installer, InstallsAppIcon;

    public function __construct(){
        $this->signature = 'native:serve {--no-queue} {--D|no-dependencies} {--installer=bun}';
        parent::__construct();
    }

    public function handle(): void
    {
        intro('Starting NativePHP dev server…');

        note('Fetching latest dependencies…');

        if (! $this->option('no-dependencies')) {
            $this->installNPMDependencies(
                force: ! $this->option('no-dependencies'),
                installer: $this->option('installer'),
                withoutInteraction: $this->option('no-interaction')
            );
        }

        note('Starting NativePHP app');

        if (PHP_OS_FAMILY === 'Darwin') {
            $this->patchPlist();
        }

        $this->patchPackageJson();

        $this->installIcon();

        $this->runDeveloper(
            installer: $this->option('installer'),
            skip_queue: true,
            withoutInteraction: $this->option('no-interaction')
        );
    }

    /**
     * Patch Electron's Info.plist to show the correct app name
     * during development.
     */
    protected function patchPlist(): void
    {
        $pList = file_get_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist');

        // Change the CFBundleName to the correct app name
        $pattern = '/(<key>CFBundleName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        $pattern = '/(<key>CFBundleDisplayName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        file_put_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist', $pList);
    }

    protected function patchPackageJson(): void
    {
        $packageJsonPath = __DIR__.'/../../resources/js/package.json';
        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        $packageJson['name'] = config('app.name', 'NativePHP');

        file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
