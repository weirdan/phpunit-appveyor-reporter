<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ServiceClientInterface;
use RuntimeException;

final class ClientFactory
{
    public function __invoke(): ServiceClientInterface
    {
        $httpClient = new Client();
        $client = new GuzzleClient(
            $httpClient,
            new Description(
                $this->loadDescription()
            )
        );
        return $client;
    }

    private function loadDescription(): array
    {
        $file = dirname(__DIR__) . '/' . 'description/description.json';

        /** @var mixed */
        $description = json_decode(
            file_get_contents($file),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($description)) {
            throw new RuntimeException('Invalid service description read from ' . $file);
        }

        $description['baseUrl'] = getenv('APPVEYOR_API_URL');

        return $description;
    }
}
