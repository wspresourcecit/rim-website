<?php

namespace App\Models;

use App\Cache\Cacheable;
use Illuminate\Database\Eloquent\Model;

class PageHeader extends Model
{
    use Cacheable;

    protected $guarded = ['id'];
}
