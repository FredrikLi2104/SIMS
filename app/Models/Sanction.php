<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $visible = ['id', 'pageid', 'title', 'dpa_id', 'started_at', 'decided_at', 'published_at', 'fine', 'currency_id', 'created_at', 'updated_at'];
    protected $appends = ['created_at_for_humans', 'started_at_for_humans', 'decided_at_for_humans', 'published_at_for_humans', 'url'];

    public function createdAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($this->created_at)->format('Y-m-d')
        );
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function decidedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->decided_at ? Carbon::parse($this->decided_at)->format('Y-m-d') : ''
        );
    }

    public function dpa()
    {
        return $this->belongsTo(Dpa::class);
    }

    public function htmlClean()
    {
        $html = $this->html;
        $r = ['started_at' => null, 'decided_at' => null, 'published_at', 'fine' => null, 'currency' => null];
        // date started
        $startedMatches = [];
        $dateStarted= preg_match('/<td>Started:<\/td>\n<td>(\d*.*)\n/m', $html, $startedMatches);
        if (isset($startedMatches[1])) {
            $r['started_at'] = $startedMatches[1];
        }
        // date decided
        $matches = [];
        $dateDecided = preg_match('/<td>Decided:<\/td>\n<td>(\d*.*)\n/m', $html, $matches);
        if (isset($matches[1])) {
            $r['decided_at'] = $matches[1];
        }
        // date published
        $publishedMatches = [];
        $datePublished = preg_match('/<td>Published:<\/td>\n<td>(\d*.*)\n/m', $html, $publishedMatches);
        if (isset($publishedMatches[1])) {
            $r['published_at'] = $publishedMatches[1];
        }
        // fine & currency
        $fineMatches = [];
        $fineRegex = preg_match('/<td>Fine:<\/td>\n<td>(\d*.*)\s(\w+)\n/m', $html, $fineMatches);
        // does it have a fine?
        if (isset($fineMatches[1])) {
            // is it legible?
            if ($fineMatches[1] != null && $fineMatches[1] != '') {
                // strip the digits in case of commas
                $r['fine'] = (int) filter_var($fineMatches[1], FILTER_SANITIZE_NUMBER_INT);
            }
        }
        // has currency?
        if (isset($fineMatches[2])) {
            // is it legible?
            if ($fineMatches[2] != null && $fineMatches[2] != '') {
                // does it exist
                $currency = Currency::where('symbol', $fineMatches[2])->first();
                if ($currency) {
                    $r['currency'] = $currency->symbol;
                }
            }
        }
        //
        return $r;
    }

    public function publishedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->published_at ? Carbon::parse($this->published_at)->format('Y-m-d') : ''
        );
    }

    public function startedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->started_at ? Carbon::parse($this->started_at)->format('Y-m-d') : ''
        );
    }

    public function url(): Attribute
    {
        return new Attribute(
            get: fn ($value) => 'https://gdprhub.eu/index.php?title=' . urlencode($this->title)
        );
    }
}
