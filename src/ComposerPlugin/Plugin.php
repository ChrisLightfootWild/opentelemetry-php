<?php

declare(strict_types=1);

namespace OpenTelemetry\ComposerPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Nevay\SPI\Composer\Plugin as SPIPlugin;

final class Plugin implements PluginInterface, EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => 'preAutoloadDump',
        ];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        // no-op
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // no-op
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Cleanup SPI.
        $spi = new SPIPlugin();
        $spi->uninstall($composer, $io);
    }

    public function preAutoloadDump(Event $event): void
    {
        // Ensure SPI runs.
        $spi = new SPIPlugin();
        $spi->preAutoloadDump($event);

        $this->prunePackageAutoloadFiles($event, $event->getComposer()->getPackage());

        $packages = $event->getComposer()->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();

        foreach ($packages as $canonicalPackage) {
            $this->prunePackageAutoloadFiles($event, $canonicalPackage);
        }
    }

    protected function prunePackageAutoloadFiles(Event $event, PackageInterface $package): void
    {
        $io = $event->getIO();
        $autoload = $package->getAutoload();
        $pruneAutoloadFiles = $package->getExtra()['opentelemetry']['prune-autoload-files'] ?? [];

        if (!$pruneAutoloadFiles || empty($autoload['files'])) {
            return;
        }

        $prunedAutoloadFiles = false;

        foreach ($autoload['files'] as $index => $file) {
            if (in_array($file, $pruneAutoloadFiles, true)) {
                $io->writeError("[OpenTelemetry Plugin] Pruned <comment>{$file}</comment> from <info>{$package->getName()}</info>");

                $prunedAutoloadFiles = true;
                unset($autoload['files'][$index]);
            }
        }

        if ($prunedAutoloadFiles && $package instanceof Package) {
            $package->setAutoload($autoload);
        }
    }
}
