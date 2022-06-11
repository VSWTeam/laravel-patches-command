<?php

namespace Vswteam\LaravelPatchesCommand\Models;

use Illuminate\Database\Eloquent\Model;

class Patches extends Model
{
    protected $table = 'patches_command';

    protected $guarded = [];

    protected $fillable = [
        'command',
        'description',
    ];
}
