<?php

declare(strict_types=1);

namespace SkachCz\Imokutr3\DI\Nette;

use Latte\Compiler\Nodes\AreaNode;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

/**
 * {imoTag $path, $width, $height [, $resizeType [, $cropType]]} ... {/imoTag}
 *
 * Placeholders in the inner content: %url%, %width%, %height%.
 *
 * @author Vladimir Skach
 */
final class ImoTagNode extends StatementNode
{
    public ExpressionNode $path;

    public ?ExpressionNode $width = null;

    public ?ExpressionNode $height = null;

    public ?ExpressionNode $fixed = null;

    public ?ExpressionNode $crop = null;

    public AreaNode $content;

    /**
     * @return \Generator<int, null, array{AreaNode, Tag|null}, static>
     */
    public static function create(Tag $tag): \Generator
    {
        $tag->expectArguments();
        $node = $tag->node = new self;
        $node->path = $tag->parser->parseExpression();

        if ($tag->parser->stream->tryConsume(',')) {
            $node->width = $tag->parser->parseExpression();
        }
        if ($tag->parser->stream->tryConsume(',')) {
            $node->height = $tag->parser->parseExpression();
        }
        if ($tag->parser->stream->tryConsume(',')) {
            $node->fixed = $tag->parser->parseExpression();
        }
        if ($tag->parser->stream->tryConsume(',')) {
            $node->crop = $tag->parser->parseExpression();
        }

        [$node->content] = yield;

        return $node;
    }

    public function print(PrintContext $context): string
    {
        $id = $context->generateId();
        $contentVar = '$imokutrContent' . $id;
        $thumbVar = '$imokutrThumb' . $id;

        return $context->format(
            <<<'XX'
                ob_start() %0.line;
                %1.node
                %2.raw = ob_get_clean();
                %3.raw = $this->global->imokutrProvider->macroThumbInterface(%4.node, %5.raw, %6.raw, %7.raw, %8.raw);
                %2.raw = str_replace(%9.raw, (string) %3.raw->width, %2.raw);
                %2.raw = str_replace(%10.raw, (string) %3.raw->height, %2.raw);
                %2.raw = str_replace(%11.raw, (string) %3.raw->url, %2.raw);
                echo %2.raw;
                XX,
            $this->position,
            $this->content,
            $contentVar,
            $thumbVar,
            $this->path,
            $this->phpExpr($context, $this->width, '0'),
            $this->phpExpr($context, $this->height, '0'),
            $this->phpExpr($context, $this->fixed, "'w'"),
            $this->phpExpr($context, $this->crop, '0'),
            "'%width%'",
            "'%height%'",
            "'%url%'",
        );
    }

    public function &getIterator(): \Generator
    {
        yield $this->path;
        if ($this->width !== null) {
            yield $this->width;
        }
        if ($this->height !== null) {
            yield $this->height;
        }
        if ($this->fixed !== null) {
            yield $this->fixed;
        }
        if ($this->crop !== null) {
            yield $this->crop;
        }
        yield $this->content;
    }

    private function phpExpr(PrintContext $context, ?ExpressionNode $node, string $default): string
    {
        return $node !== null ? $node->print($context) : $default;
    }
}
