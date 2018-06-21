<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table='metas';

    protected $primaryKey='mid';

    protected $fillable=['count'];
}
