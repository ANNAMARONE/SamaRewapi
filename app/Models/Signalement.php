<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    /** @use HasFactory<\Database\Factories\SignalementFactory> */
    use HasFactory;
    protected $guarded=[];
public function user(){
    return $this->belongsTo(User::class);
}
    public function categorie(){
        return $this->belongsTo(Categorie::class);
    }
    public function images()
{
    return $this->hasMany(Image::class);
}
public function resolutions(){
    return $this->hasMany(Resolution::class);
}
public function votes(){
    return $this->hasMany(Vote::class);
}
public function commentaires(){
    return $this->hasMany(Commentaire::class);
}
}