<?php

namespace Thedevopser\AnsibleComposerPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Process\Process;
use Thedevopser\AnsibleComposerPlugin\Provider\AnsibleComposerPluginCommandProvider;

class AnsibleComposerPlugin implements PluginInterface, EventSubscriberInterface, Capable
{
    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'post-install-cmd' => 'onPostInstall',
            'post-update-cmd' => 'onPostInstall',
        ];
    }

    public function getCapabilities(): array
    {
        return [
            'Composer\Plugin\Capability\CommandProvider' => AnsibleComposerPluginCommandProvider::class,
        ];
    }

    public function onPostInstall()
    {
        $this->io->write('<info>AnsibleComposerPlugin:</info> Installation en cours...');

        $process = Process::fromShellCommandline('composer thedevopser:ansible:install');
        $process->run();

        $this->io->write($process->getOutput());
        $this->io->write('<info>AnsibleComposerPlugin:</info> Installation terminée.');
    }
    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }
}
