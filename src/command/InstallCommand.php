<?php

namespace native\thinkElectron\command;

use RuntimeException;
use think\console\Command;

use native\thinkElectron\traits\Installer;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;

class InstallCommand extends Command
{
    use LaravelCommand;
    use Installer;

    public function __construct()
    {
        $this->signature = 'native:install
        {--force : Overwrite existing files by default}
        {--installer=npm : The package installer to use: npm, yarn or pnpm}';
        $this->description = 'Install all of the NativePHP resources';
        parent::__construct();
    }

    public function handle(): void
    {
        intro('Publishing NativePHP Service Provider...');

        $withoutInteraction = $this->option('no-interaction');

        $this->call('vendor:pub', ['--tag' => 'nativephp-provider']);
        $this->call('vendor:pub', ['--tag' => 'nativephp-config']);

        $this->installComposerScript();

        $installer = $this->getInstaller($this->option('installer'), $withoutInteraction);

        $this->installNPMDependencies(
            force: $this->option('force'),
            installer: $installer,
            withoutInteraction: $withoutInteraction
        );

        $shouldPromptForServe = ! $withoutInteraction && ! $this->option('force');

        if ($shouldPromptForServe && confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve', [
                '--installer' => $installer,
                '--no-dependencies',
                '--no-interaction' => $withoutInteraction,
            ]);
        }

        outro('NativePHP scaffolding installed successfully.');
    }

    /**
     * @throws \Throwable
     */
    private function installComposerScript(): void
    {
        info('Installing `composer native:dev` script alias...');

        $composer = json_decode(file_get_contents(base_path('composer.json')));
        throw_unless($composer, RuntimeException::class, "composer.json couldn't be parsed");

        $composerScripts = $composer->scripts ?? (object) [];

        if ($composerScripts->{'native:dev'} ?? false) {
            note('native:dev script already installed... skipping.');

            return;
        }

        $composerScripts->{'native:dev'} = [
            'Composer\\Config::disableProcessTimeout',
            'npx concurrently -k -c "#93c5fd,#c4b5fd" "php artisan native:serve --no-interaction" "npm run dev" --names=app,vite',
        ];

        data_set($composer, 'scripts', $composerScripts);

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        note('native:dev script installed!');
    }
}
