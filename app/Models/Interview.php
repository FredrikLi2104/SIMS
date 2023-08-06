<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['agenda', 'user_id', 'creator_id'];
    

    public function statements() {
        return $this->belongsToMany(Statement::class);
    }

}
