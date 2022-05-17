<?php

namespace Thedevopser\AnsibleComposerPlugin\Provider;

use Composer\Plugin\Capability\CommandProvider;
use Thedevopser\AnsibleComposerPlugin\Command\AnsibleComposerPluginCommand;
use Thedevopser\AnsibleComposerPlugin\Command\AnsibleInstall;

class AnsibleComposerPluginCommandProvider implements CommandProvider
{

    public function getCommands(): array
    {
        return [
            new AnsibleInstall(__DIR__.'/../../../../../ansible'),
            new AnsibleComposerPluginCommand(),
        ];
    }
}
