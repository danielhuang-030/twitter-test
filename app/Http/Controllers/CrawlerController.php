<?php

namespace App\Http\Controllers;

use App\Console\Commands\Crawler\BaseCommand as CrawlerCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CrawlerController extends Controller
{
    public function checked(Request $request, string $crawler, string $monitor)
    {
        $redisKey = CrawlerCommand::getRedisKeyForStopLineNotity($monitor, $crawler);
        if (!Redis::exists($redisKey)) {
            return 'not exist.';
        }
        if (!Redis::delete($redisKey)) {
            return 'failed to delete.';
        }

        return 'checked.';
    }
}
