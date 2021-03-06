<?php

namespace Weirdan\PhpUnitAppVeyorReporter;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ServiceClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

final class ClientFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(): ServiceClientInterface
    {
        $stack = HandlerStack::create();
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $stack->push(Middleware::log(
            $this->logger,
            new MessageFormatter(MessageFormatter::DEBUG)
        ));
        $httpClient = new Client(['handler' => $stack]);
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

        $description['baseUri'] = getenv('APPVEYOR_API_URL');

        return $description;
    }
}
