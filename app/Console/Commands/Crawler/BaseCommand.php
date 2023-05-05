<?php

namespace App\Console\Commands\Crawler;

use Carbon\Carbon;
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
    public const STOP_LINE_NOTITY_TIME = '22:30';

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
            14
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
            return static::FAILURE;
        }

        foreach ($monitors as $monitor) {
            if (Redis::exists(static::getRedisKeyForStopLineNotity($monitor))) {
                continue;
            }

            $url = sprintf(data_get(static::getMonitorDataList(), sprintf('%s.monitor', $monitor)), $monitor);
            try {
                $response = $this->client->get($url, [
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
                $this->notityByLine(vsprintf("notity stopping. %s \n( checked: %s )", [
                    $th->getMessage(),
                    static::getCheckedUrl($monitor),
                ]), $monitor);

                return static::FAILURE;
            }
        }

        return static::SUCCESS;
    }

    public static function getRedisKeyForStopLineNotity(string $monitor, string $crawler = ''): string
    {
        $crawler = (!empty($crawler) ? $crawler : str((new \ReflectionClass(static::class))->getShortName())->snake());

        return vsprintf('notity:line:stopping:%s:%s', [
            $crawler,
            $monitor,
        ]);
    }

    public static function setNotityStoppingUtil(string $monitor): bool
    {
        // calculating time
        $baseTime = Carbon::now();
        $notityTime = Carbon::parse(static::STOP_LINE_NOTITY_TIME);
        if ($baseTime->greaterThan($notityTime)) {
            $notityTime = $notityTime->addDays(1);
        }

        return Redis::setex(static::getRedisKeyForStopLineNotity($monitor), $baseTime->diffInSeconds($notityTime), true);
    }

    protected function notityByLine(string $message, string $monitor): bool
    {
        if (Redis::get(static::getRedisKeyForStopLineNotity($monitor))) {
            return false;
        }

        $result = Line::send($message);
        if ($result) {
            static::setNotityStoppingUtil($monitor);
        }

        return $result;
    }

    protected static function getMonitors(): array
    {
        return array_keys(static::getMonitorDataList());
    }

    protected static function getCheckedUrl($monitor)
    {
        return vsprintf('%s/crawler/index/%s/%s', [
            config('app.url'),
            str((new \ReflectionClass(static::class))->getShortName())->snake(),
            $monitor,
        ]);
    }

    abstract protected function executeAndNotify(array $responseData, $monitor): bool;

    abstract protected static function getMonitorDataList(): array;
}
