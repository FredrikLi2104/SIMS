<?php

namespace Database\Seeders;

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
        // currencies
        $sanctions = Sanction::all();
        foreach ($sanctions as $sanction) {
            $html = $sanction->html;
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
