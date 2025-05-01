<?php

namespace yangweijie\thinkElectron\updater\contracts;

interface Updater
{
    public function environmentVariables(): array;

    public function builderOptions(): array;
}
