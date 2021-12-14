<?php

namespace Daynnnnn\StatamicDatabase\Roles;

use Illuminate\Database\Eloquent\Model;
use Statamic\Support\Arr;

class RolesModel extends Model
{
    protected $guarded = [];

    protected $table = 'roles';

    protected $casts = [
        'data' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAttribute($key)
    {
        return Arr::get($this->getAttributeValue('data'), $key, parent::getAttribute($key));
    }
}
