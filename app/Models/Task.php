<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $appends = ['start_for_humans', 'end_for_humans', 'title_truncated', 'title_en_plain', 'title_se_plain', 'desc_en_plain', 'desc_se_plain'];

    public function action()
    {
        return $this->hasOne(Action::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    protected function startForHumans(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => Carbon::parse($attribute['start'])->format('Y-m-d')
        );
    }

    protected function endForHumans(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => Carbon::parse($attribute['end'])->format('Y-m-d')
        );
    }

    protected function titleTruncated(): Attribute
    {
        $locale = App::currentLocale();
        return Attribute::make(get: function ($value, $attribute) use ($locale) {
            $title = $attribute["title_$locale"];
            // Extract plain text from Quill Delta JSON if present
            $title = $this->extractTextFromQuill($title);
            return Str::limit($title, 40);
        });
    }

    protected function titleEnPlain(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => $this->extractTextFromQuill($attribute['title_en'] ?? '')
        );
    }

    protected function titleSePlain(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => $this->extractTextFromQuill($attribute['title_se'] ?? '')
        );
    }

    protected function descEnPlain(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => $this->extractTextFromQuill($attribute['desc_en'] ?? '')
        );
    }

    protected function descSePlain(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attribute) => $this->extractTextFromQuill($attribute['desc_se'] ?? '')
        );
    }

    /**
     * Extract plain text from Quill Delta JSON format
     */
    private function extractTextFromQuill($text)
    {
        if (empty($text)) {
            return '';
        }

        // Check if it's Quill JSON format
        if (is_string($text) && str_starts_with($text, '{"ops":[')) {
            try {
                $delta = json_decode($text, true);
                if (isset($delta['ops']) && is_array($delta['ops'])) {
                    $plainText = '';
                    foreach ($delta['ops'] as $op) {
                        if (isset($op['insert'])) {
                            $plainText .= $op['insert'];
                        }
                    }
                    return trim($plainText);
                }
            } catch (\Exception $e) {
                // If parsing fails, return original
                return $text;
            }
        }

        return $text;
    }
}
