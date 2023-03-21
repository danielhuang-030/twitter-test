<?php

namespace App\Http\Controllers;

use App\Console\Commands\Crawler\BaseCommand as CrawlerCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CrawlerController extends Controller
{
    public function index(Request $request, string $crawler, string $monitor)
    {
        $redisKey = CrawlerCommand::getRedisKeyForStopLineNotity($monitor, $crawler);
        if (!Redis::exists($redisKey)) {
            return view('crawler.index', [
                'not_exist' => true,
            ]);
        }

        return view('crawler.index', [
            'checked_url' => sprintf('/crawler/checked/%s/%s', $crawler, $monitor),
        ]);
    }

    public function checked(Request $request, string $crawler, string $monitor)
    {
        $redisKey = CrawlerCommand::getRedisKeyForStopLineNotity($monitor, $crawler);
        if (!Redis::delete($redisKey)) {
            return 'failed to delete.';
        }

        return 'checked.';
    }

    protected function checkRedisKey(string $crawler, string $monitor): bool
    {
        $redisKey = CrawlerCommand::getRedisKeyForStopLineNotity($monitor, $crawler);
        if (!Redis::exists($redisKey)) {
            return 'not exist.';
        }
    }
}
