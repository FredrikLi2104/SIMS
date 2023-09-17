<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['agenda', 'interviewee', 'creator_id', 'organisation_id', 'plan_id'];
    

    public function statements() {
        return $this->belongsToMany(Statement::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

}
