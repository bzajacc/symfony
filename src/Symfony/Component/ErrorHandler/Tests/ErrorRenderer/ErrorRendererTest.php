<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ErrorHandler\Tests\ErrorRenderer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorRendererTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\ErrorHandler\Exception\ErrorRendererNotFoundException
     * @expectedExceptionMessage No error renderer found for format "foo".
     */
    public function testErrorRendererNotFound()
    {
        $exception = FlattenException::create(new \Exception('foo'));
        (new ErrorRenderer([]))->render($exception, 'foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Error renderer "stdClass" must implement "Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface".
     */
    public function testInvalidErrorRenderer()
    {
        $exception = FlattenException::create(new \Exception('foo'));
        (new ErrorRenderer([new \stdClass()]))->render($exception, 'foo');
    }

    public function testCustomErrorRenderer()
    {
        $renderers = [new FooErrorRenderer()];
        $errorRenderer = new ErrorRenderer($renderers);

        $exception = FlattenException::create(new \RuntimeException('Foo'));
        $this->assertSame('Foo', $errorRenderer->render($exception, 'foo'));
    }
}

class FooErrorRenderer implements ErrorRendererInterface
{
    public static function getFormat(): string
    {
        return 'foo';
    }

    public function render(FlattenException $exception): string
    {
        return $exception->getMessage();
    }
}