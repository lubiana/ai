<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Agent\Toolbox\Event;

use Symfony\AI\Platform\Tool\Tool;

/**
 * Dispatched after the arguments are denormalized, just before invoking the tool.
 *
 * @author Valtteri R <valtzu@gmail.com>
 */
final readonly class ToolCallArgumentsResolved
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function __construct(
        public object $tool,
        public Tool $metadata,
        public array $arguments,
    ) {
    }
}
