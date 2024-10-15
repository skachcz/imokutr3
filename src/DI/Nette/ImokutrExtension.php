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

use Nette\PhpGenerator\Factory;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Extensions\DefinitionSchema;
use Nette\DI\Factor;

use Latte\Engine;

use Tracy\Debugger;

/**
 * Imokutr Nette extension (for Nette 3.x)
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

        /** @var FactoryDefinition */
        $latteFactory = $builder->getDefinition('latte.latteFactory');
        $latteFactory->getResultDefinition()
                    ->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);


        $builder->addDefinition($this->prefix('imokutrFilters'))
        ->setFactory(ImokutrFilters::class, [$this->imokutrConfig]);

        $latteFactory
        ->getResultDefinition()
        ->addSetup('?->addFilter(?, ?)', ['@self', 'imoUrl', [$this->prefix('@imokutrFilters'), 'imoUrl']]);

        /*
        $method = '?->onCompile[] = function($engine)  {
                SkachCz\Imokutr3\DI\Nette\ImokutrMacros::install($engine->getCompiler());
            }';
        $latteFactory->getResultDefinition()->addSetup($method, ['@self']);
*/
        $latteFactory
            ->getResultDefinition()
            ->addSetup(ImokutrMacros::class . '::install(?->getCompiler())', ['@self']);


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
