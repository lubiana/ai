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
use Symfony\AI\Platform\Bridge\OpenAI\GPT;
use Symfony\AI\Platform\Bridge\OpenAI\PlatformFactory;
use Symfony\AI\Platform\Bridge\OpenAI\TokenOutputProcessor;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload.php';
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env');

if (!isset($_SERVER['OPENAI_API_KEY'])) {
    echo 'Please set the OPENAI_API_KEY environment variable.'.\PHP_EOL;
    exit(1);
}

$platform = PlatformFactory::create($_SERVER['OPENAI_API_KEY']);
$model = new GPT(GPT::GPT_4O_MINI, [
    'temperature' => 0.5, // default options for the model
]);

$agent = new Agent($platform, $model, outputProcessors: [new TokenOutputProcessor()]);
$messages = new MessageBag(
    Message::forSystem('You are a pirate and you write funny.'),
    Message::ofUser('What is the Symfony framework?'),
);
$response = $agent->call($messages, [
    'max_tokens' => 500, // specific options just for this call
]);

$metadata = $response->getMetadata();

echo 'Utilized Tokens: '.$metadata['total_tokens'].\PHP_EOL;
echo '-- Prompt Tokens: '.$metadata['prompt_tokens'].\PHP_EOL;
echo '-- Completion Tokens: '.$metadata['completion_tokens'].\PHP_EOL;
echo 'Remaining Tokens: '.$metadata['remaining_tokens'].\PHP_EOL;
