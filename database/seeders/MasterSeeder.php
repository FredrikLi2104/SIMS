<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Currency;
use App\Models\Sanction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $sanctions = Sanction::all();
        foreach ($sanctions as $sanction) {
            $html = $sanction->html;
            // [complete]
            // currencies 
            /*
            $matches = [];
            $regex = preg_match('/<td>Fine:<\/td>\n<td>(\d*.*)\s(\w+)\n/m', $html, $matches);
            // does it have a fine?
            if(isset($matches[1])) {
                // is it legible?
                if($matches[1] != null && $matches[1] != '') {
                    // strip the digits in case of commas
                    $fine = (int) filter_var($matches[1], FILTER_SANITIZE_NUMBER_INT);
                    $sanction->update(['fine' => $fine]);
                }
            }
            // has currency?
            if(isset($matches[2])) {
                // is it legible?
                if($matches[2] != null && $matches[2] != '') {
                    // does it exist?
                    $currency = Currency::where('symbol', $matches[2])->first();
                    if($currency) {
                        $sanction->update(['currency_id' => $currency->id]);
                    } else {
                        // create it
                        $currency = Currency::create(['symbol' => $matches[2]]);
                        $sanction->update(['currency_id' => $currency->id]);
                    }
                }
            }
            
            // started_at
            $startedMatches = [];
            $startedAtRegex = preg_match('/<td>Started:<\/td>\n<td>(\d*.*)\n/m', $html, $startedMatches);
            if (isset($startedMatches[1])) {
                if ($startedMatches[1] != null && $startedMatches[1] != '' && strlen($startedMatches[1]) > 7) {
                    try {
                        $sanction->update(['started_at' => Carbon::parse($startedMatches[1])]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
            // published_at
            $publishedMatches = [];
            $publishedAtRegex = preg_match('/<td>Published:<\/td>\n<td>(\d*.*)\n/m', $html, $publishedMatches);
            if (isset($publishedMatches[1])) {
                if ($publishedMatches[1] != null && $publishedMatches[1] != '' && strlen($publishedMatches[1]) > 7) {
                    try {
                        $sanction->update(['published_at' => Carbon::parse($publishedMatches[1])]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
            */
            // articles
            // get relevant laws if any
            $relevantLawMatches = [];
            $relevantRegex = preg_match('/<td>Relevant Law:<\/td>\n<td>(.*)\n<\/td>/m', $html, $relevantLawMatches);
            // has law?
            if (isset($relevantLawMatches[1])) {
                // law is not empty or weird space?
                if ($relevantLawMatches[1] != null && $relevantLawMatches[1] != '') {
                    $relevantHtml = $relevantLawMatches[1];
                    $articleMatches = [];
                    $articleRegex = preg_match_all('/href="(?P<link>[^"]*)"[^>]*>(?P<title>[^<]*)/m', $relevantHtml, $articleMatches);
                    if (isset($articleMatches[1])) {
                        if ($articleMatches[1] != null && $articleMatches[1] != '') {
                            for ($i = 0; $i < count($articleMatches['link']); $i++) {
                                // find if article exists
                                $article = Article::where('title', $articleMatches['title'][$i])->first();
                                if (!$article) {
                                    // fix link
                                    $linkPrefix = substr($articleMatches['link'][$i], 0, 6);
                                    if ($linkPrefix != '/index') {
                                        $url = $articleMatches['link'][$i];
                                    } else {
                                        $url = 'https://gdprhub.eu/' . $articleMatches['link'][$i];
                                    }
                                    // fix title
                                    if (strlen($articleMatches['title'][$i]) > 128) {
                                        $title = substr($articleMatches['title'][$i], 0, 125) . '...';
                                    } else {
                                        $title = $articleMatches['title'][$i];
                                    }
                                    $article = Article::create(['title' => mb_convert_encoding($title, 'UTF-8'), 'url' => $url]);
                                } else {
                                    $sanction->articles()->attach($article);
                                }
                            }
                        }
                    }
                }
            }
        }
        /*
        $sanction = Sanction::where('id', 480)->first();
        $html = $sanction->html;
        $matches = [];
        $dateDecided = preg_match('/<td>Decided:<\/td>\n<td>(\d*.*)\n/m', $html, $matches);
        $decidedAt = Carbon::parse($matches[1]);
        //$decidedAt = Carbon::createFromFormat('d.m.Y', $matches[1]);
        $response = Http::get('https://gdprhub.eu/api.php', [
            'action' => 'parse',
            'format' => 'json',
            'pageid' => '3610',
        ]);
        if($response->successful()) {
            $data = json_decode($response->body());
            $html = json_decode(json_encode($data->parse->text), true);
            $html = $html['*'];
            $matches = [];
            $dateDecided = preg_match('/<td>Decided:<\/td>\n<td>(\d*.*)\n/m', $html, $matches);
            var_dump($dateDecided, $matches);
        }
        */
    }
}
