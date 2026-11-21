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

use Chevere\Filesystem\AssertPathFormat;
use Chevere\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Filesystem\Exceptions\PathNotAbsoluteException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AssertPathFormatTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new AssertPathFormat('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new AssertPathFormat('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AssertPathFormat('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new AssertPathFormat('/some/../dir');
    }

    public static function dataUnix(): array
    {
        return [
            [
                '/', '/',
            ],
            [
                '/A', '/A',
            ],
            [
                '/A/B/C/', '/A/B/C/',
            ],
        ];
    }

    #[DataProvider('dataUnix')]
    public function testUnix(string $path, string $expected): void
    {
        $assert = new AssertPathFormat($path);
        $this->assertSame($expected, $assert->path());
        $this->assertSame('', $assert->drive());
    }

    public static function dataWindows(): array
    {
        return [
            [
                '\\', '/',
            ],
            [
                '\A', '/A',
            ],
            [
                '\Program Files\Custom Utilities\\', '/Program Files/Custom Utilities/',
            ],
            [
                '\Program Files', '/Program Files',
            ],
        ];
    }

    #[DataProvider('dataWindows')]
    public function testWindows(string $path, string $expected): void
    {
        $assert = new AssertPathFormat($path);
        $this->assertSame($expected, $assert->path());
        $this->assertSame('', $assert->drive());
    }

    public static function dataWindowsDriveLetters(): array
    {
        return [
            [
                'a:\\', 'a:/',
            ],
            // [
            //     'A:\Documents', 'A:/Documents',
            // ],
            // [
            //     'B:\Documents\Newsletters\\', 'B:/Documents/Newsletters/',
            // ],
            // [
            //     'C:\Documents\Newsletters/', 'C:/Documents/Newsletters/',
            // ],
        ];
    }

    #[DataProvider('dataWindowsDriveLetters')]
    public function testWindowsDriveLetters(string $path, string $expected): void
    {
        $assert = new AssertPathFormat($path);
        $letter = $expected[0];
        $this->assertSame($expected, $assert->path());
        $this->assertSame($letter, $assert->drive());
    }

    public static function dataDrivePaths(): array
    {
        return [
            // [
            //     'phar://some/path', 'phar',
            // ],
            [
                'phar:///Users/rodolfo/git/chevere/xrdebug/xrdebug.phar/app/',
                'phar',
            ],
        ];
    }

    #[DataProvider('dataDrivePaths')]
    public function testDrivePaths(string $path, string $drive): void
    {
        $assert = new AssertPathFormat($path);
        $this->assertSame($path, $assert->path());
        $this->assertSame($drive, $assert->drive());
    }
}
