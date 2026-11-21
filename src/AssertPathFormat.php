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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Filesystem\Exceptions\PathNotAbsoluteException;
use Chevere\Filesystem\Interfaces\AssertPathFormatInterface;
use Chevere\Regex\Regex;

final class AssertPathFormat implements AssertPathFormatInterface
{
    private string $drive = '';

    public function __construct(
        private string $path
    ) {
        $this->path = str_replace('\\', '/', $path);
        $this->drive = $this->getDrive();
        $this->assertAbsolutePath();
        $this->assertNoDoubleDots();
        $this->assertNoDots();
        if ($this->drive === '') {
            $this->assertNoExtraSlashes();
        }
    }

    public function path(): string
    {
        return $this->path;
    }

    public function drive(): string
    {
        return $this->drive;
    }

    private function getDrive(): string
    {
        $regex = new Regex('/([\w]+)\:\/{1,2}[^\s]*/');
        $matches = $regex->match($this->path);

        return $matches[1] ?? '';
    }

    private function assertAbsolutePath(): void
    {
        if ($this->drive === '' && ! str_starts_with($this->path, '/')) {
            throw new PathNotAbsoluteException(
                sprintf(
                    'Path `%s` must start with `%s`',
                    $this->path,
                    '/'
                )
            );
        }
    }

    private function assertNoDoubleDots(): void
    {
        if (strpos($this->path, '../') !== false) {
            throw new PathDoubleDotsDashException(
                sprintf(
                    'Must omit `%s` for path `%s`',
                    '../',
                    $this->path
                )
            );
        }
    }

    private function assertNoDots(): void
    {
        if (strpos($this->path, './') !== false) {
            throw new PathDotSlashException(
                sprintf(
                    'Must omit %s for path %s',
                    './',
                    $this->path
                )
            );
        }
    }

    private function assertNoExtraSlashes(): void
    {
        if (strpos($this->path, '//') !== false) {
            throw new PathExtraSlashesException(
                sprintf(
                    'Path `%s` contains extra-slashes',
                    $this->path
                )
            );
        }
    }
}
