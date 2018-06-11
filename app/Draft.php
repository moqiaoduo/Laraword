<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    protected $table='drafts';

    protected $fillable=['post_id','uid','title','content'];
}
