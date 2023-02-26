<?php

namespace App\Console\Commands\Crawler;

use Illuminate\Support\Facades\Redis;

class RutenProduct extends BaseCommand
{
    public const URL_MONITOR = 'https://www.ruten.com.tw/item/show?%s';
    public const HTML_PATTERN = '#RT\.context = (\{.+\});#m';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:ruten_p';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler ruten product';

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

                // for HTML
                $html = (string) $response->getBody();
                preg_match(static::HTML_PATTERN, $html, $matches);
                if (empty($matches[1])) {
                    throw new \Exception('Unable to get response data');
                }
                $responseData = json_decode($matches[1], true);

                $this->executeAndNotify($responseData, $monitor);
            } catch (\Throwable $th) {
                dd($th->getMessage());
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
                'total' => 0,
            ],
        ];
    }

    protected function executeAndNotify(array $responseData = [], $monitor): bool
    {
        // check total
        $total = data_get($responseData, 'item.remainNum', 0);

        // rules
        if (
            !data_get($responseData, 'item.isSoldEnd', true) &&
            data_get($monitor, 'total') < $total
        ) {
            // notity
            $this->notityByLine(vsprintf("items arrived. %s \n( checked: %s )", [
                sprintf((string) data_get(static::getMonitorDataList(), sprintf(sprintf('%s.monitor', $monitor), $monitor)), $monitor),
                static::getCheckedUrl($monitor),
            ]), $monitor);
        }

        return true;
    }
}
