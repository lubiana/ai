<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\AI\Agent\Agent;
use Symfony\AI\Platform\Bridge\Google\Gemini;
use Symfony\AI\Platform\Bridge\Google\PlatformFactory;
use Symfony\AI\Platform\Message\Content\Audio;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');

if (!isset($_SERVER['GEMINI_API_KEY'])) {
    echo 'Please set the GEMINI_API_KEY environment variable.'.\PHP_EOL;
    exit(1);
}

$platform = PlatformFactory::create($_SERVER['GEMINI_API_KEY']);
$model = new Gemini(Gemini::GEMINI_1_5_FLASH);

$agent = new Agent($platform, $model);
$messages = new MessageBag(
    Message::ofUser(
        'What is this recording about?',
        Audio::fromFile(dirname(__DIR__, 2).'/fixtures/audio.mp3'),
    ),
);
$response = $agent->call($messages);

echo $response->getContent().\PHP_EOL;
