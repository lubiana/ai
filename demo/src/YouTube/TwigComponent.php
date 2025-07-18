<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\YouTube;

use Psr\Log\LoggerInterface;
use Symfony\AI\Platform\Message\MessageInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

use function Symfony\Component\String\u;

#[AsLiveComponent('youtube')]
final class TwigComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly Chat $youTube,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[LiveAction]
    public function start(#[LiveArg] string $videoId): void
    {
        if (str_contains($videoId, 'youtube.com')) {
            $videoId = $this->getVideoIdFromUrl($videoId);
        }

        try {
            $this->youTube->start($videoId);
        } catch (\Exception $e) {
            $this->logger->error('Unable to start YouTube chat.', ['exception' => $e]);
            $this->youTube->reset();
        }
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages(): array
    {
        return $this->youTube->loadMessages()->withoutSystemMessage()->getMessages();
    }

    #[LiveAction]
    public function submit(#[LiveArg] string $message): void
    {
        $this->youTube->submitMessage($message);
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->youTube->reset();
    }

    private function getVideoIdFromUrl(string $url): string
    {
        $query = parse_url($url, \PHP_URL_QUERY);

        if (!$query) {
            throw new \InvalidArgumentException('Unable to parse YouTube URL.');
        }

        return u($query)->after('v=')->before('&')->toString();
    }
}
