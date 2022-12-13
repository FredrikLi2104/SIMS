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
    protected $visible = ['id', 'pageid', 'title', 'desc_en', 'desc_se', 'dpa_id', 'started_at', 'decided_at', 'published_at', 'fine', 'currency_id', 'created_at', 'updated_at'];
    protected $appends = ['created_at_for_humans', 'started_at_for_humans', 'decided_at_for_humans', 'published_at_for_humans', 'url'];

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }

    public function createdAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn($value) => Carbon::parse($this->created_at)->format('Y-m-d')
        );
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function decidedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->decided_at ? Carbon::parse($this->decided_at)->format('Y-m-d') : ''
        );
    }

    public function dpa()
    {
        return $this->belongsTo(Dpa::class);
    }

    public function htmlClean()
    {
        $html = $this->html;
        $r = ['articles' => [], 'started_at' => null, 'decided_at' => null, 'published_at', 'fine' => null, 'currency' => null];
        // date started
        $startedMatches = [];
        $dateStarted = preg_match('/<td>Started:<\/td>\n<td>(\d*.*)\n/m', $html, $startedMatches);
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
                $r['fine'] = (int)filter_var($fineMatches[1], FILTER_SANITIZE_NUMBER_INT);
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
                        for ($i = 0; $i < count($articleMatches['title']); $i++) {
                            // sanitize
                            if (strlen($articleMatches['title'][$i]) > 128) {
                                $title = substr($articleMatches['title'][$i], 0, 125) . '...';
                            } else {
                                $title = $articleMatches['title'][$i];
                            }
                            $r['articles'][] = $title;
                        }
                    }
                }
            }
        }
        //
        return $r;
    }

    public function publishedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->published_at ? Carbon::parse($this->published_at)->format('Y-m-d') : ''
        );
    }

    public function sni()
    {
        return $this->belongsTo(Sni::class);
    }

    public function startedAtForHumans(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->started_at ? Carbon::parse($this->started_at)->format('Y-m-d') : ''
        );
    }

    public function url(): Attribute
    {
        return new Attribute(
            get: fn($value) => 'https://gdprhub.eu/index.php?title=' . urlencode($this->title)
        );
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function outcome()
    {
        return $this->belongsTo(Outcome::class);
    }

    public function issue_category()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
