<?php

namespace App\Console\Commands\Crawler;

class RutenStore extends BaseCommand
{
    public const URL_MONITOR = 'https://rapi.ruten.com.tw/api/users/v1/index.php/%s/storeinfo';
    public const URL_STORE = 'https://www.ruten.com.tw/store/%s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:ruten_store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler ruten stores';

    protected static function getMonitorDataList(): array
    {
        return [
            '123' => [
                'monitor' => static::URL_MONITOR,
                'total' => 1,
            ],
        ];
    }

    protected function executeAndNotify(array $responseData, $monitor): bool
    {
        // check total
        $total = (int) data_get(static::getMonitorDataList(), sprintf('%s.total', $monitor));

        // rules
        if (
            'success' == data_get($responseData, 'status') &&
            $total != data_get($responseData, 'data.items_cnt', 0)
        ) {
            // notity
            $this->notityByLine(vsprintf("items count changed. %s \n( checked: %s )", [
                vsprintf(static::URL_STORE, [
                    $monitor,
                ]),
                static::getCheckedUrl($monitor),
            ]), $monitor);
        }

        return true;
    }
}
