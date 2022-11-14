<?php

namespace App\Console\Commands\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Phattarachai\LineNotify\Facade\Line;

abstract class BaseCommand extends Command
{
    public const URL_MONITOR = '';
    public const URL_SHOW = '';
    public const STOP_LINE_NOTITY_TIME = 3600;

    protected $client;

    public function __construct()
    {
        parent::__construct();

        // message formatter
        $clientMessageFormatter = new MessageFormatter('{req_headers} - {req_body} - {res_headers} - {res_body} - {error}');

        // logger
        $loggerName = sprintf('crawler/%s', str((new \ReflectionClass($this))->getShortName())->snake());
        $logger = new Logger($loggerName);
        $handler = new RotatingFileHandler(
            sprintf('%s/%s/log.log', storage_path('logs'), $loggerName),
            60
        );
        $handler->setFormatter(new JsonFormatter());
        $logger->pushHandler($handler);

        // init client with handler
        $stack = HandlerStack::create();
        $stack->push(Middleware::log($logger, $clientMessageFormatter));

        // init client
        $this->client = new Client([
            'handler' => $stack,
            'timeout' => 10,
        ]);
    }

    public function handle()
    {
        $monitors = static::getMonitors();
        if (empty($monitors)) {
            return Command::FAILURE;
        }

        foreach ($monitors as $monitor) {
            $url = sprintf(static::URL_MONITOR, $monitor);
            try {
                $response = $this->client->get($url, [
                    'curl' => [
                        CURLOPT_TCP_KEEPALIVE => 1,
                    ],
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
                    ],
                ]);
                $responseData = json_decode($response->getBody(), true);
                if (empty($responseData)) {
                    throw new \Exception('Unable to get response data');
                }

                $this->executeAndNotify($responseData, $monitor);
            } catch (\Throwable $th) {
                // notity stopping
                $this->notityByLine(sprintf('notity stopping. %s', $th->getMessage()));
                Redis::setex(static::getRedisKeyForStopLineNotity(), static::STOP_LINE_NOTITY_TIME, true);

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    public static function getRedisKeyForStopLineNotity(): string
    {
        return 'notity:line:stopping';
    }

    protected function notityByLine(string $message): bool
    {
        if (Redis::get(static::getRedisKeyForStopLineNotity())) {
            return false;
        }

        return Line::send($message);
    }

    abstract protected static function getMonitors(): array;

    abstract protected function executeAndNotify(array $responseData, $monitor): bool;
}
