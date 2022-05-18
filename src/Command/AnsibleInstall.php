<?php

namespace Thedevopser\AnsibleComposerPlugin\Command;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class AnsibleInstall extends BaseCommand
{
    /**
     * @var string
     */
    private $ansibleDirectory;

    public function __construct(string $ansibleDirectory)
    {
        parent::__construct();
        $this->ansibleDirectory = $ansibleDirectory;
    }

    protected function configure()
    {
        $this->setName('thedevopser:ansible:install');
        $this->setDescription('Install Ansible');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $composerSystem = new \Composer\Util\Filesystem();

        if (is_dir($this->ansibleDirectory)) {
            $output->writeln('<info>Installation déjà effectuée. Merci de supprimer le dossier ansible à la racine de votre projet pour le réinstaller</info>');
            return Command::SUCCESS;
        }

        $filesystem->mkdir($this->ansibleDirectory);

        $helper = $this->getHelper('question');
        $question = new Question("<question>Quel est le type de projet ? (legacy / symfony)</question>", 'symfony');
        $type = $helper->ask($input, $output, $question);

        if ($type === "legacy") {
            $output->writeln('<info>Installation de Ansible pour les applications legacy</info>');

            $composerSystem->copy(__DIR__ . '/../../ansible/legacy', $this->ansibleDirectory);
            $composerSystem->copy(__DIR__ . '/../../ansible/hosts.yml', $this->ansibleDirectory . '/hosts.yml');

            return Command::SUCCESS;
        }

        if ($type === "symfony") {
            $output->writeln('<info>Installation de Ansible pour les applications symfony</info>');

            $composerSystem->copy(__DIR__ . '/../../ansible/symfony', $this->ansibleDirectory);
            $composerSystem->copy(__DIR__ . '/../../ansible/hosts.yml', $this->ansibleDirectory . '/hosts.yml');

            return Command::SUCCESS;
        }

        $output->writeln('<info>Installation terminée avec succès!</info>');

        return Command::SUCCESS;
    }
}
