<?php

namespace native\thinkElectron;

use Symfony\Component\Process\Process;

class ProcessWrapper
{
    private $process;
    private array $environment;
    private string $path;
    /**
     * @var null
     */
    private $timeout;
    private bool $tty;
    private $quietly;
    private $command;
    private $idleTimeout;
    private $input;

    public function __construct()
    {

    }

    public function path(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the additional environment variables for the process.
     *
     * @param  array  $environment
     * @return $this
     */
    public function env(array $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Indicate that the process may run forever without timing out.
     *
     * @return $this
     */
    public function forever()
    {
        $this->timeout = null;

        return $this;
    }

    /**
     * Enable TTY mode for the process.
     *
     * @param  bool  $tty
     * @return $this
     */
    public function tty(bool $tty = true)
    {
        $this->tty = $tty;

        return $this;
    }

    public function run(array|string|null $command = null, ?callable $callback = null)
    {
        $this->command = $command ?: $this->command;
        $process = $this->toSymfonyProcess($command);
        return $process->run($callback, $this->environment);
    }

    /**
     * Get a Symfony Process instance from the current pending command.
     *
     * @param  array<array-key, string>|string|null  $command
     * @return Process
     */
    protected function toSymfonyProcess(array|string|null $command)
    {
        $command = $command ?? $this->command;

        $process = is_iterable($command)
            ? new Process($command, null, $this->environment)
            : Process::fromShellCommandline((string) $command, null, $this->environment);

        $process->setWorkingDirectory((string) ($this->path ?? getcwd()));
        $process->setTimeout($this->timeout);

        if ($this->idleTimeout) {
            $process->setIdleTimeout($this->idleTimeout);
        }

        if ($this->input) {
            $process->setInput($this->input);
        }

        if ($this->quietly) {
            $process->disableOutput();
        }

        if ($this->tty) {
            $process->setTty(true);
        }

        if (! empty($this->options)) {
            $process->setOptions($this->options);
        }

        return $process;
    }
}