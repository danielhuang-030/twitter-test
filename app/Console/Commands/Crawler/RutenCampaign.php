<?php

namespace App\Console\Commands\Crawler;

class RutenCampaign extends BaseCommand
{
    public const URL_MONITOR = 'https://rapi.ruten.com.tw/api/users/v1/%s/campaigns/rightnow?event_type=all';
    public const URL_STORE = 'https://www.ruten.com.tw/store/%s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:ruten_c';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler ruten campaigns';

    protected static function getMonitorDataList(): array
    {
        return [
            'order-buy0923617020' => [
                'monitor'   => static::URL_MONITOR,
                'promotion' => static::URL_STORE,
                'total'     => 1,
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
            $total != data_get($responseData, 'data.total_count', 0)
        ) {
            // notity
            $this->notityByLine(sprintf('found campaigns. %s', vsprintf(static::URL_SHOW, [
                $monitor,
                // data_get($responseData, 'data.list.0.event_id'),
            ])), $monitor);
        }

        return true;
    }
}
