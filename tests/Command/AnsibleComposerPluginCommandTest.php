<?php

declare(strict_types=1);

namespace Thedevopser\AnsibleComposerPlugin\Test\Command;

use Composer\Console\Application;
use Composer\Util\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;

use Symfony\Component\Finder\Finder;
use Thedevopser\AnsibleComposerPlugin\Command\AnsibleInstall;

class AnsibleComposerPluginCommandTest extends TestCase
{
    const DIR_ANSIBLE_SOURCE = __DIR__ . '/../../ansible';
    const DIR_ANSIBLE_TARGET = __DIR__ . '/../ansible-target';
    const ANSIBLE_ALREADY_INSTALL_MESSAGE = 'Installation déjà effectuée. Merci de supprimer le dossier ansible à la racine de votre projet pour le réinstaller';

    public function testCreateAnsibleDir()
    {
        $command = $this->getCommand(self::DIR_ANSIBLE_TARGET);
        $return = $command->execute([]);

        $this->assertTrue(is_dir(self::DIR_ANSIBLE_TARGET));
    }

    public function testCopyFileForLegacyApplication()
    {
        $command = $this->getCommand(self::DIR_ANSIBLE_TARGET);
        $command->setInputs(['legacy']);
        $return = $command->execute([]);

        $finder = new Finder();
        $finder->files()->in(self::DIR_ANSIBLE_SOURCE.'/legacy');

        foreach ($finder as $file) {
            $this->assertFileExists(self::DIR_ANSIBLE_TARGET . '/' . $file->getRelativePathname());
        }

        $this->assertFileExists(self::DIR_ANSIBLE_TARGET . '/hosts.yml');
    }

    public function testCopyFileForSymfonyApplication()
    {
        $command = $this->getCommand(self::DIR_ANSIBLE_TARGET);
        $command->setInputs(['symfony']);
        $return = $command->execute([]);

        $finder = new Finder();
        $finder->files()->in(self::DIR_ANSIBLE_SOURCE.'/symfony');

        foreach ($finder as $file) {
            $this->assertFileExists(self::DIR_ANSIBLE_TARGET . '/' . $file->getRelativePathname());
        }

        $this->assertFileExists(self::DIR_ANSIBLE_TARGET . '/hosts.yml');
    }

    public function testDirAlreadyCreated()
    {
        $fileSytem = new \Symfony\Component\Filesystem\Filesystem();
        $fileSytem->mkdir(self::DIR_ANSIBLE_TARGET);

        $command = $this->getCommand(self::DIR_ANSIBLE_TARGET);
        $command->setInputs(['symfony']);
        $return = $command->execute([]);

        $finder = new Finder();
        $finder->files()->in(self::DIR_ANSIBLE_TARGET);

        $this->assertSame(0, iterator_count($finder));
        $this->assertStringContainsString(self::ANSIBLE_ALREADY_INSTALL_MESSAGE, $command->getDisplay());
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
