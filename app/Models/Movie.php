<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'year',
        'duration',
    ];


    public function classification(){
        return $this->belongsTo('App\Models\Classification');
    }

    public function directors(){
        return $this->belongsToMany('App\Models\Director','movie_director');
    }

    public function actors(){
        return $this->belongsToMany('App\Models\Actor','movie_actor');
    }
}
