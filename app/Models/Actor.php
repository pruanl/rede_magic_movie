<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'age',
        'sex'
    ];

    public function movies(){
        return $this->belongsToMany('App\Models\Movie','movie_actor');
    }
}
