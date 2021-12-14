<?php

namespace Realtydev\StatamicDatabase\Roles;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Statamic\Facades\Site;

use Statamic\Facades\Role as RoleFacade;
use Statamic\Auth\File\Role as FileRole;

class Roles extends FileRole
{
    protected $model;


    public static function fromModel(RolesModel $model)
    {
        $data = $model->data;
        return RoleFacade::make()
        ->handle($model->handle)
        ->title(array_get($data, 'title') ?? null)
        ->addPermission(array_get($data, 'permissions', []) ?? null)
        ->preferences(array_get($data, 'preferences', []) ?? null);
    }


    public function toModel()
    {
        $data = array('title' => $this->title,'permissions' => $this->permissions,'preferences' => $this->preferences);

        $model = RolesModel::firstOrNew([
            'handle' => $this->id(),
        ]);

        $model->data = $data;
        $model->save();

        return $model;
    }

    public function model($model = null)
    {
        if (func_num_args() === 0) {
            return $this->model;
        }

        $this->model = $model;

        $this->id($model->id);

        return $this;
    }

    public function addPermission($permission)
    {
        $this->permissions = $this->permissions
            ->merge(Arr::wrap($permission))
            ->unique()
            ->values();

        return $this;
    }

    public function lastModified()
    {
        return $this->model->updated_at;
    }



}
