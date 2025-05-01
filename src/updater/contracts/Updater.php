<?php

namespace native\thinkElectron\updater\contracts;

interface Updater
{
    public function environmentVariables(): array;

    public function builderOptions(): array;
}
