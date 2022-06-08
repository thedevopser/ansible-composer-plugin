<?php

namespace Thedevopser\AnsibleComposerPlugin\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class MakeFileInstall extends BaseCommand
{

    const MESSAGE_MAKEFILE_INSTALLED = '<info>thedevopser/ansible-composer-plugin: Makefile déjà présent, ajout de la commande</info>';
    const MESSAGE_MAKEFILE_CONTENT_IS_UP_TO_DATE = '<info>thedevopser/ansible-composer-plugin: [skip] Makefile est à jour</info>';
    const MAKEFILE_DIR = __DIR__ . '/../../make/Makefile';

    /**
     * @var string
     */
    private $makefile;

    public function __construct(string $makefile)
    {
        parent::__construct();
        $this->makefile = $makefile;
    }

    protected function configure()
    {
        $this->setName('thedevopser:makefile:install');
        $this->setDescription('Install Makefile');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $composerSystem = new \Composer\Util\Filesystem();

        $model = 'include vendor/thedevopser/ansible-composer-plugin/make/make.mk';

        if (file_exists($this->makefile) === false) {
            $composerSystem->copy(self::MAKEFILE_DIR, $this->makefile);
            return Command::SUCCESS;
        }

        $content = file_get_contents($this->makefile);
        $lines = explode("\n", $content);

        if (in_array($model, $lines)) {
            $output->writeln(self::MESSAGE_MAKEFILE_CONTENT_IS_UP_TO_DATE);
            return Command::SUCCESS;
        }

        $output->writeln(self::MESSAGE_MAKEFILE_INSTALLED);
        file_put_contents($this->makefile, "\n\n$model", FILE_APPEND);

        return Command::SUCCESS;
    }
}
