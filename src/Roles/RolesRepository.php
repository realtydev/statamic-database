<?php

namespace Realtydev\StatamicDatabase\Roles;

use Illuminate\Support\Collection;
use Statamic\Auth\RoleRepository as BaseRepository;
use Statamic\Contracts\Auth\Role as RoleContract;
use Statamic\Facades\Blink;

class RolesRepository extends BaseRepository
{

    protected $handle;


    public static function bindings(): array
    {
        return [
            RoleContract::class => Roles::class,
        ];
    }

    public function all(): Collection
    {
        return Blink::once('roles', function () {
            $keys = RolesModel::get()->map(function ($model) {
                return Roles::fromModel($model);
            });

            return Collection::make($keys);
        });
    }

    public function find($handle): ?Roles {
        return Blink::once('role::'.$handle, function () use ($handle) {
            if (($model = RolesModel::where('handle', $handle)->first()) == null) {
                return null;
            }

            return Roles::fromModel($model);
        });
    }

    public function findByHandle($handle): ?Roles
    {
        return $this->find($handle);
    }

    public function save($role)
    {

        $model = $role->toModel();

        $model->save();

        $role->model($model->fresh());
    }

    public function delete($role)
    {
        $role->toModel()->delete();
    }

    public function make(string $handle = null): RoleContract
    {
        return (new Roles)->handle($handle);
    }
}
