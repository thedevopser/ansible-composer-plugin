<?php

declare(strict_types=1);

namespace Thedevopser\AnsibleComposerPlugin\Test\Command;

use Composer\Console\Application;
use Composer\Util\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;

use Thedevopser\AnsibleComposerPlugin\Command\AnsibleInstall;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

class AnsibleComposerPluginCommandTest extends TestCase
{
    const DIR_ANSIBLE_SOURCE = __DIR__ . '/../../ansible';
    const DIR_ANSIBLE_TARGET = __DIR__ . '/../ansible-target';

    public function testCreateAnsibleDir()
    {
        $command = $this->getCommand(self::DIR_ANSIBLE_TARGET);
        $return = $command->execute([]);

        $this->assertTrue(is_dir(self::DIR_ANSIBLE_TARGET));
    }

    public function tearDown()
    {
        if (is_dir(self::DIR_ANSIBLE_TARGET)) {
            $fileSystem = new Filesystem();
            $fileSystem->removeDirectory(self::DIR_ANSIBLE_TARGET);
        }
    }

    private function getCommand($directory)
    {
        $command = new AnsibleInstall($directory);

        $application = new Application();
        $application->add($command);

        $command = $application->find('thedevopser:ansible:install');

        return new CommandTester($command);

    }
}
