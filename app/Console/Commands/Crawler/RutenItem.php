<?php

namespace App\Console\Commands\Crawler;

class RutenItem extends BaseCommand
{
    public const URL_MONITOR = 'https://rapi.ruten.com.tw/api/campaigns/v1/%d/items?sort=rnk%%2Fdc&per_page=50&page=1&detail=true';
    public const URL_SHOW = 'https://www.ruten.com.tw/store/%s/campaign?sort=rnk%%2Fdc&eventId=%d&p=1';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:ruten_i';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler ruten campaigns';

    protected static function getMonitors(): array
    {
        return [
            305422,
        ];
    }

    protected static function getMonitorTotalPairs(): array
    {
        return [
            305422 => 3,
        ];
    }

    protected function executeAndNotify(array $responseData, $monitor): bool
    {
        // check total
        $total = data_get(static::getMonitorTotalPairs(), $monitor, 0);

        // rules
        if (
            'success' == data_get($responseData, 'status') &&
            (int) data_get($responseData, 'data.total_count') != $total
        ) {
            // notity
            $this->notityByLine(sprintf('items changed. %s', vsprintf(static::URL_SHOW, [
                data_get($responseData, 'data.list.0.seller_nick'),
                $monitor,
            ])), $monitor);
        }

        return true;
    }
}
