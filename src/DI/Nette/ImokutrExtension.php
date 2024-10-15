<?php
namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\ImokutrConfig;
use SkachCz\Imokutr3\DI\Nette\ExtensionTools;

use SkachCz\Imokutr3\Exception\ImokutrNetteMissingExtension;

use SkachCz\Imokutr3\DI\Nette\ImokutrFilters;
use SkachCz\Imokutr3\DI\Nette\ImokutrMacros;

use Nette\Schema\Schema;
use Nette\Schema\Expect;

use Nette\DI\CompilerExtension;
use Nette\DI\Extensions\DefinitionSchema;
use Tracy\Debugger;

// if (!class_exists('Nette\DI\CompilerExtension')) {
    //throw new ImokutrNetteMissingExtension();
// }

/**
 * Imokutr Nette extension (for Nette 2.4)
 *
 * @package SkachCz\Imokutr\Nette
 * @author  Vladimir Skach
 */
final class ImokutrExtension extends CompilerExtension
{
    public ImokutrConfig $imokutrConfig;

    /**
     * @return void
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        Debugger::barDump($this->config, "load config imokutr");

        //imokutr config provider:
        // $builder->addDefinition($this->prefix('imokutrProvider'))
            // ->setFactory(Imokutr::class, [$this->config]);
        /*
        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->config]);
        */
        $this->imokutrConfig = ExtensionTools::createConfigFromArray($this->config);

        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->imokutrConfig]);
    }

    /**
     * @return void
     */
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $methodExists = method_exists($builder->getDefinition('latte.latteFactory'), 'getResultDefinition');

        if ($methodExists) {
            /* nette 3.0: */
            $builder->getDefinition('latte.latteFactory')
                ->getResultDefinition()
                ->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        } else {
            $builder->getDefinition('latte.latteFactory')
                ->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        }

        if ($builder->hasDefinition('nette.latteFactory')) {
            $factory = $builder->getDefinition('nette.latteFactory');

            // filter registration:
            $filters = new ImokutrFilters($this->imokutrConfig);

            $factory->getResultDefinition()->addSetup('addFilter', ['imoUrl', [$filters, 'imoUrl']]);

            // macro registration:
            $method = '?->onCompile[] = function($engine)  {
                SkachCz\Imokutr3\DI\Nette\ImokutrMacros::install($engine->getCompiler());
            }';

            $factory->getResultDefinition()->addSetup($method, ['@self']);
        }
    }

    public function getConfigSchema(): Schema
    {
        $scheme = Expect::structure([
        'originalRootPath' => Expect::string(),
            'thumbsRootPath' => Expect::string(),
            'thumbsRootRelativePath' => Expect::string(),
            'defaultImageRelativePath' => Expect::string()->default('default.png'),
            'qualityJpeg' => Expect::int()->default(75),
            'qualityPng' => Expect::int()->default(6),
        ]);

        Debugger::log($scheme, "imokutr scheme");

        return $scheme;
    }
}
