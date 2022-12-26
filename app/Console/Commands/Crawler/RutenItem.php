<?php

namespace App\Console\Commands\Crawler;

class RutenItem extends BaseCommand
{
    public const URL_PROMOTION = 'https://www.ruten.com.tw/store/%s/campaign?sort=rnk%%2Fdc&eventId=%d&p=1';

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

    protected static function getMonitorDataList(): array
    {
        return [
            314827 => [
                'monitor'   => 'https://rapi.ruten.com.tw/api/campaigns/v1/%d/items?sort=rnk%%2Fdc&per_page=50&page=1&detail=true',
                'promotion' => static::URL_PROMOTION,
                'total'     => 89,
            ],
            314896 => [
                'monitor'   => 'https://rapi.ruten.com.tw/api/campaigns/v1/%d/items?sort=rnk%%2Fdc&per_page=50&page=1&detail=true',
                'promotion' => static::URL_PROMOTION,
                'total'     => 372,
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
            (int) data_get($responseData, 'data.total_count') != $total
        ) {
            // notity
            $this->notityByLine(vsprintf("items changed. %s \n( checked: %s )", [
                vsprintf((string) data_get(static::getMonitorDataList(), sprintf('%s.promotion', $monitor)), [
                    data_get($responseData, 'data.list.0.seller_nick'),
                    $monitor,
                ]),
                static::getCheckedUrl($monitor),
            ]), $monitor);
        }

        return true;
    }
}
