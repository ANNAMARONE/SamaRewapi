<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    /** @use HasFactory<\Database\Factories\ResolutionFactory> */
    use HasFactory;
     protected $guarded=[];
      
     public function user()
     {
         return $this->belongsTo(User::class, 'users_id');
     }
     public function signalement(){
        return $this->belongsTo(Signalement::class);
     }
}