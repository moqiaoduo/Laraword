<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table='metas';

    protected $primaryKey='mid';

    protected $fillable=['count'];

    public function metaContent(){
        return $this->belongsToMany('App\Content','relationships','mid','cid');
    }
}
