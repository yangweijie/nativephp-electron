<?php

namespace native\thinkElectron\updater;


use yangweijie\thinkElectron\updater\contracts\Updater;

class SpacesProvider implements Updater
{
    public function __construct(protected array $config) {}

    public function environmentVariables(): array
    {
        return [
            'DO_KEY_ID' => $this->config['key'],
            'DO_SECRET_KEY' => $this->config['secret'],
        ];
    }

    public function builderOptions(): array
    {
        return [
            'provider' => 'spaces',
            'name' => $this->config['name'],
            'region' => $this->config['region'],
            'path' => $this->config['path'],
        ];
    }
}
