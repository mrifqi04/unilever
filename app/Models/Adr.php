<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adr extends Model
{
    protected $table = 'public.adr_counter';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';
}
