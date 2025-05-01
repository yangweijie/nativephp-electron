<?php

namespace yangweijie\thinkElectron\command;

use think\facade\Console;
use think\console\Command;
use Illuminate\Support\Facades\Artisan;
use yangweijie\thinkElectron\concerns\LocatesPhpBinary;
use yangweijie\thinkElectron\traits\LaravelCommand;
use yangweijie\thinkElectron\traits\OsAndArch;

class PublishCommand extends Command
{
    use LaravelCommand;
    use LocatesPhpBinary;
    use OsAndArch;

    protected $signature = 'native:publish
        {os? : The operating system to build for (linux, mac, win)}
        {arch? : The Processor Architecture to build for (x64, x86, arm64)}';

    protected array $availableOs = ['win', 'linux', 'mac'];

    public function handle(): void
    {
        $this->info('Building and publishing NativePHP app…');

        $os = $this->selectOs($this->argument('os'));

        $arch = $this->selectArchitectureForOs($os, $this->argument('arch'));

        Console::call('native:build', ['os' => $os, 'arch' => $arch, '--publish' => true], 'console');
    }
}
