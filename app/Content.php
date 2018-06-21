<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table='contents';

    protected $primaryKey='cid';

    protected $fillable=['parent','uid','title','content','slug','type'];

    public function contentMeta(){
        return $this->belongsToMany('App\Meta','relationships','cid','mid');
    }
}
