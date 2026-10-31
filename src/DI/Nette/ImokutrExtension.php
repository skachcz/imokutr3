<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\DI\Nette;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\ImokutrConfig;

/**
 * Imokutr Nette extension (Nette 3.2+ / Latte 3)
 *
 * @author Vladimir Skach
 */
final class ImokutrExtension extends CompilerExtension
{
    public ImokutrConfig $imokutrConfig;

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $this->imokutrConfig = ExtensionTools::createConfigFromArray($this->config);

        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->imokutrConfig]);
    }

    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        /** @var FactoryDefinition $latteFactory */
        $latteFactory = $builder->getDefinition('latte.latteFactory');

        $builder->addDefinition($this->prefix('imokutrFilters'))
            ->setFactory(ImokutrFilters::class, [$this->imokutrConfig]);

        $builder->addDefinition($this->prefix('latteExtension'))
            ->setFactory(ImokutrLatteExtension::class);

        $latteFactory->getResultDefinition()
            ->addSetup('addExtension', ['@' . $this->prefix('latteExtension')]);
    }

    public function getConfigSchema(): Schema
    {
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
