<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Platform\Bridge\Voyage;

use Symfony\AI\Platform\Exception\RuntimeException;
use Symfony\AI\Platform\Model;
use Symfony\AI\Platform\Response\ResponseInterface as LlmResponse;
use Symfony\AI\Platform\Response\VectorResponse;
use Symfony\AI\Platform\ResponseConverterInterface;
use Symfony\AI\Platform\Vector\Vector;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final readonly class ResponseConverter implements ResponseConverterInterface
{
    public function supports(Model $model): bool
    {
        return $model instanceof Voyage;
    }

    public function convert(ResponseInterface $response, array $options = []): LlmResponse
    {
        $response = $response->toArray();

        if (!isset($response['data'])) {
            throw new RuntimeException('Response does not contain embedding data');
        }

        $vectors = array_map(fn (array $data) => new Vector($data['embedding']), $response['data']);

        return new VectorResponse($vectors[0]);
    }
}
