<?php

namespace Thedevopser\AnsibleComposerPlugin\Command;

use Composer\Command\BaseCommand;
use Composer\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
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
        $this->setDescription('Installs Ansible files depending on the project type');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force reinstall even if Ansible directory already exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $composerSystem = new \Composer\Util\Filesystem();

        if (is_dir($this->ansibleDirectory)) {
            if (!$input->getOption('force')) {
                $output->writeln('Le répertoire ansible existe déjà. Veuillez utiliser l\'option --force ou supprimer le dossier pour réinstaller');
                return 1; // return non-zero to indicate error
            } else {
                $filesystem->remove($this->ansibleDirectory); // Delete directory if --force option is present
            }
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Sélectionner votre type de projet',
            ['legacy', 'symfony'], // The choices
            0 // The default choice is the first one
        );
        $question->setErrorMessage('La réponse %s est invalide.');

        $type = $helper->ask($input, $output, $question);
        $output->writeln('Vous avez choisi : ' . $type);

        $filesystem->mkdir($this->ansibleDirectory);

        $filesystem->copy(__DIR__ . '/../../ansible/'. $type . '/' . $type . '.yml', $this->ansibleDirectory . '/' . $type . '.yml');
        $filesystem->copy(__DIR__ . '/../../ansible/hosts.yml', $this->ansibleDirectory . '/hosts.yml');
        $filesystem->copy(__DIR__ . '/../../ansible/vars.yml', $this->ansibleDirectory . '/vars.yml');


        $output->writeln('Installation complete.');
        return 0;
    }
}
