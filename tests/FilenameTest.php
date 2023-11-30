<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests;

use Chevere\Filesystem\Filename;
use InvalidArgumentException;
use LengthException;
use PHPUnit\Framework\TestCase;

final class FilenameTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Filename(' ');
    }

    public function testValidLength(): void
    {
        $basename = str_repeat('e', 255);
        $this->expectNotToPerformAssertions();
        new Filename($basename);
    }

    public function testInvalidLength(): void
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionCode(110);
        new Filename(str_repeat('a', 256));
    }

    public function testWithExtension(): void
    {
        $name = 'test';
        $extension = 'JPEG';
        $filename = "{$name}.{$extension}";
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->__toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }

    public function testWithoutExtension(): void
    {
        $name = 'test';
        $extension = '';
        $filename = $name;
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->__toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }

    public function testWithoutName(): void
    {
        $name = '';
        $extension = 'png';
        $filename = "{$name}.{$extension}";
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->__toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }
}
