<?php

namespace App\Console\Commands\Crawler;

use Illuminate\Support\Facades\Redis;

class RutenSearch extends BaseCommand
{
    public const URL_MONITOR = 'https://rtapi.ruten.com.tw/api/search/v3/index.php/core/seller/%d/prod?%s';
    public const URL_SEARCH = 'https://www.ruten.com.tw/store/%s/find?%s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:ruten_s';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler ruten search';

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

            $monitorData = data_get(static::getMonitorDataList(), $monitor);
            $url = vsprintf(data_get($monitorData, 'monitor'), [
                data_get($monitorData, 'seller'),
                http_build_query([
                    'sort' => 'new/dc',
                    'q' => data_get($monitorData, 'query'),
                    'limit' => 30,
                    'offset' => 1,
                ]),
            ]);
            try {
                $response = $this->client->get($url, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
                    ],
                ]);
                $responseData = json_decode($response->getBody(), true);

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

    protected static function getMonitorDataList(): array
    {
        return [
            '123' => [
                'monitor' => static::URL_MONITOR,
                'seller' => 123,
                'query' => '關鍵字',
                'total' => 1,
            ],
        ];
    }

    protected function executeAndNotify(array $responseData = [], $monitor): bool
    {
        // check total
        $total = data_get($responseData, 'TotalRows', 0);

        // get monitor data
        $monitorData = data_get(static::getMonitorDataList(), $monitor);

        // rules
        if (
            data_get($monitorData, 'total') < $total
        ) {
            // notity
            $this->notityByLine(vsprintf("search have been added. %s \n( checked: %s )", [
                vsprintf(static::URL_SEARCH, [
                    $monitor,
                    http_build_query([
                        'sort' => 'new/dc',
                        'q' => data_get($monitorData, 'query'),
                        'p' => 1,
                    ]),
                ]),
                static::getCheckedUrl($monitor),
            ]), $monitor);
        }

        return true;
    }
}
