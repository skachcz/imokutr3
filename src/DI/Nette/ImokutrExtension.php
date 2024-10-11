<?php
namespace SkachCz\Imokutr3\DI\Nette;

use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\Config;
use SkachCz\Imokutr3\Exception\ImokutrNetteMissingExtension;

use SkachCz\Imokutr3\DI\Nette\ImokutrFilters;
use SkachCz\Imokutr3\DI\Nette\ImokutrMacros;

use Nette\Schema\Schema;
use Nette\Schema\Expect;

use Nette\DI\CompilerExtension;


use Tracy\Debugger;

// if (!class_exists('Nette\DI\CompilerExtension')) {
    //throw new ImokutrNetteMissingExtension();
// }

/**
 * Imokutr Nette extension (for Nette 2.4)
 *
 * @package SkachCz\Imokutr\Nette
 * @author Vladimir Skach
 */
final class ImokutrExtension extends CompilerExtension
{
    /**
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return void
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        //imokutr config provider:
        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->config]);
        /*
        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->config]);
        */
        $builder->addDefinition($this->prefix('articles'))
            ->setFactory(Imokutr::class, ['@connection']) // or setCreator()
            ->addSetup('setLogger', ['@logger']);

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
                ->getResultDefinition()->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        } else {
            $builder->getDefinition('latte.latteFactory')
                ->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        }

        if ($builder->hasDefinition('nette.latteFactory')) {

            $factory = $builder->getDefinition('nette.latteFactory');

            // filter registration:
            $filters = new ImokutrFilters($this->config);


            if ($methodExists) {
                /* nette 3.0 */
                $factory->getResultDefinition()->addSetup('addFilter', ['imoUrl', [$filters, 'imoUrl']]);
            } else {
                $factory->addSetup('addFilter', ['imoUrl', [$filters, 'imoUrl']]);
            }

            // macro registration:
            $method = '?->onCompile[] = function($engine)  {
                SkachCz\Imokutr\Nette\ImokutrMacros::install($engine->getCompiler());
            }';

            if ($methodExists) {
                /* nette 3.0 */
                $factory->getResultDefinition()->addSetup($method, ['@self']);
            } else {
                $factory->addSetup($method, ['@self']);
            }

        }
    }

    public function getConfigSchema(): Schema
    {
        Debugger::log("imokutr start", 'imokutr');

        return Expect::structure([
			'originalRootPath' => Expect::string(),
            'thumbsRootPath' => Expect::string(),
            'thumbsRootRelativePath' => Expect::string(),
            'defaultImageRelativePath' => Expect::string()->default('default.png'),
            'qualityJpeg' => Expect::int()->default(75),
            'qualityPng' => Expect::int()->default(6),
		]);
    }

}
