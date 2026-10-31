<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\DI\Nette;

use Latte\Extension;
use SkachCz\Imokutr3\Imokutr;

/**
 * Latte 3 extension: {imoTag} and the imoUrl filter.
 *
 * @author Vladimir Skach
 */
final class ImokutrLatteExtension extends Extension
{
    public function __construct(
        private readonly Imokutr $imokutr,
        private readonly ImokutrFilters $filters,
    ) {
    }

    /**
     * @return array<string, callable>
     */
    #[\Override]
    public function getTags(): array
    {
        return [
            'imoTag' => ImoTagNode::create(...),
        ];
    }

    /**
     * @return array<string, callable>
     */
    #[\Override]
    public function getFilters(): array
    {
        return [
            'imoUrl' => $this->filters->imoUrl(...),
        ];
    }

    /**
     * @return array<string, object>
     */
    #[\Override]
    public function getProviders(): array
    {
        return [
            'imokutrProvider' => $this->imokutr,
        ];
    }
}
