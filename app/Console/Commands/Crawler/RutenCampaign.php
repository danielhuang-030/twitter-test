<?php

namespace App\Console\Commands\Crawler;

class RutenCampaign extends BaseCommand
{
    public const URL_MONITOR = 'https://rapi.ruten.com.tw/api/users/v1/%s/campaigns/rightnow?event_type=all';
    public const URL_SHOW = 'https://www.ruten.com.tw/store/qzchuyi1/campaign?sort=rnk%2Fdc&eventId=%d&p=1';

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

    protected static function getMonitors(): array
    {
        return [
            'qzchuyi1',
        ];
    }

    protected function executeAndNotify(array $responseData, $monitor): bool
    {
        // dd($responseData, $monitor);
        // if (data_get($responseData, 'status')) {
        // } else {
        // }

        return true;
    }
}
