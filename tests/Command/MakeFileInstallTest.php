<?php

namespace Thedevopser\AnsibleComposerPlugin\Test\Command;

use Composer\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Thedevopser\AnsibleComposerPlugin\Command\MakeFileInstall;

class MakeFileInstallTest extends TestCase
{
    const MESSAGE_MAKEFILE_INSTALLED = 'Makefile déjà présent, ajout de la commande';
    const MESSAGE_MAKEFILE_CONTENT_IS_UP_TO_DATE = 'thedevopser/ansible-composer-plugin: [skip] Makefile est à jour';
    const MAKEFILE_DIR = __DIR__ . '/../../make/Makefile';

    /**
     * @var string
     */
    private $makefileDestination;

    public function setUp()
    {
        $this->makefileDestination = __DIR__ . '/../Makefile';
    }

    public function testMakefileAlreadyExistsWithNoContent()
    {
        $this->tearDown();

        $fileSystem = new Filesystem();
        $fileSystem->touch($this->makefileDestination);

        $command =$this->getCommand($this->makefileDestination);
        $command->execute([]);

        $this->assertStringContainsString(self::MESSAGE_MAKEFILE_INSTALLED, $command->getDisplay());
    }

    public function testMakefileAlreadyExistWithGoodContent()
    {
        $this->tearDown();

        $fileSystem = new Filesystem();
        $fileSystem->copy(__DIR__ . '/../Makefile.test', $this->makefileDestination);

        $command =$this->getCommand($this->makefileDestination);
        $command->execute([]);

        $this->assertStringContainsString(self::MESSAGE_MAKEFILE_CONTENT_IS_UP_TO_DATE, $command->getDisplay());
    }

    public function testCopyMakefile()
    {
        $command =$this->getCommand($this->makefileDestination);
        $command->execute([]);

        $this->assertFileExists($this->makefileDestination);
    }

    public function tearDown()
    {
        if (file_exists($this->makefileDestination)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($this->makefileDestination);
        }
    }

    private function getCommand($file)
    {
        $command = new MakeFileInstall($file);

        $application = new Application();
        $application->add($command);

        $command = $application->find('thedevopser:makefile:install');

        return new CommandTester($command);

    }
}
