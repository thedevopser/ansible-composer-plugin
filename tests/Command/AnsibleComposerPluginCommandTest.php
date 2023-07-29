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
    const ANSIBLE_ALREADY_INSTALL_MESSAGE = 'Le répertoire ansible existe déjà. Veuillez utiliser l\'option --force ou supprimer le dossier pour réinstaller';

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
        $finder->files()->in(self::DIR_ANSIBLE_SOURCE . '/legacy');

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
        $finder->files()->in(self::DIR_ANSIBLE_SOURCE . '/symfony');

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

    public function testConfigure()
    {
        $command = new AnsibleInstall(self::DIR_ANSIBLE_TARGET);
        $definition = $command->getDefinition();

        // Test the command name
        $this->assertEquals('thedevopser:ansible:install', $command->getName());

        // Test the command description
        $this->assertEquals('Installs Ansible files depending on the project type', $command->getDescription());

        // Test the --force option
        $this->assertTrue($definition->hasOption('force'));
        $forceOption = $definition->getOption('force');
        $this->assertEquals('f', $forceOption->getShortcut());
        $this->assertEquals('Force reinstall even if Ansible directory already exists', $forceOption->getDescription());
    }

    public function tearDown(): void
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
