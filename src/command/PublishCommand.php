<?php

namespace native\thinkElectron\command;

use think\facade\Console;
use think\console\Command;
use native\thinkElectron\concerns\LocatesPhpBinary;
use native\thinkElectron\traits\LaravelCommand;
use native\thinkElectron\traits\OsAndArch;

class PublishCommand extends Command
{
    use LaravelCommand;
    use LocatesPhpBinary;
    use OsAndArch;

    protected string $signature = 'native:publish
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
