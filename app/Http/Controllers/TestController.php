<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function index(Request $request)
    {
        // info("Request", [$request->all()]);

        $site = $request->url ?? 'https://www.bbc.com/';
        $url = request()->fullUrl() ?? $site;

        if (Str::startsWith($url, rtrim(env('APP_URL'), DIRECTORY_SEPARATOR))) {
            $url = str_replace(
                rtrim(env('APP_URL'), 
                DIRECTORY_SEPARATOR), 
                rtrim($site, '/'), 
                $url
            );
        }

        info("Final URL: ", [$url]);

        $process = new Process(['python3', '/var/www/html/python/scraping/scrapy_test1/main.py']);
        $process = Process::fromShellCommandline('scrapy crawl spyder', '/var/www/html/python/scraping/scrapy_test1/webscrapy');
        $process = Process::fromShellCommandline(
            'scrapy fetch ' . $url,
            null,
            null,
            null,
            30
        ); # WORKING

        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $data = $process->getOutput();

        return $data;

        // return exec('scrapy fetch https://www.apple.com/', $data, $retrive);
    }
}
