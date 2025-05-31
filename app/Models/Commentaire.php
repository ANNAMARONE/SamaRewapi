<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    /** @use HasFactory<\Database\Factories\CommentaireFactory> */
    use HasFactory;
     
    protected $guarded=[];
public function user(){
    return $this->belongsTo(User::class);
}
public function signalement(){
    return $this->belongsTo(Signalement::class);
}
}