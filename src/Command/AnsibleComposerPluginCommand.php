<?php

namespace Thedevopser\AnsibleComposerPlugin\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnsibleComposerPluginCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('thedevopser:install');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('thedevopser:ansible:install');
        $command->run($input, $output);

        return Command::SUCCESS;
    }
}
