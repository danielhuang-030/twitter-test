<?php

namespace App\Console\Commands\Crawler;

class Dokii extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:dokii';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler dokii toys';

    protected static function getMonitorDataList(): array
    {
        return [
            23374 => [
                'monitor'   => 'https://www.dokiitoys.com/promotion/promotionproductsajax/%d?page=2',
                'promotion' => 'https://www.dokiitoys.com/onsale/1P1M/%d',
                'total'     => 1,
            ],
        ];
    }

    protected function executeAndNotify(array $responseData = [], $monitor): bool
    {
        // items
        $items = data_get($responseData, 'standards', []);

        // check total
        $total = (int) data_get(static::getMonitorDataList(), sprintf('%s.total', $monitor));

        // rules
        if (
            !data_get($responseData, 'status') ||
            count($items) != $total
        ) {
            // notity
            $this->notityByLine(vsprintf("items changed. %s \n( checked: %s )", [
                sprintf((string) data_get(static::getMonitorDataList(), sprintf(sprintf('%s.promotion', $monitor), $monitor)), $monitor),
                static::getCheckedUrl($monitor),
            ]), $monitor);
        }

        return true;
    }
}
