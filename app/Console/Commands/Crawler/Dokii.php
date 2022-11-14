<?php

namespace App\Console\Commands\Crawler;

class Dokii extends BaseCommand
{
    public const URL_MONITOR = 'https://www.dokiitoys.com/promotion/promotionproductsajax/%d?page=4';
    public const URL_SHOW = 'https://www.dokiitoys.com/onsale/1111M111M/%d';

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

    protected static function getMonitors(): array
    {
        return [
            22602,
        ];
    }

    protected static function getMonitorTotalPairs(): array
    {
        return [
            22602 => 3,
        ];
    }

    protected function executeAndNotify(array $responseData = [], $monitor): bool
    {
        // items
        $items = data_get($responseData, 'standards', []);

        // check total
        $total = data_get(static::getMonitorTotalPairs(), $monitor, 0);
        $total = 2;

        // rules
        if (
            !data_get($responseData, 'status') ||
            count($items) != $total
        ) {
            // notity
            $this->notityByLine(sprintf('items changed. %s', sprintf(static::URL_SHOW, $monitor)));
        }

        return true;
    }
}
