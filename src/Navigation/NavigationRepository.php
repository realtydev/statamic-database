<?php

namespace Realtydev\StatamicDatabase\Navigation;

use Statamic\Stache\Repositories\NavigationRepository as StacheRepository;
use Statamic\Contracts\Structures\Nav as NavContract;
use Statamic\Facades\Blink;
use Illuminate\Support\Collection;

class NavigationRepository extends StacheRepository
{
    public static function bindings(): array
    {
        return [
            NavContract::class => Nav::class,
        ];
    }

    public function all(): Collection
    {
        return Blink::once('navs', function () {
            $keys = NavigationModel::get()->map(function ($model) {
                return Nav::fromModel($model);
            });

            return Collection::make($keys);
        });
    }

    public function find($handle): ?Nav {
        return Blink::once('nav::'.$handle, function () use ($handle) {
            if (($model = NavigationModel::where('handle', $handle)->first()) == null) {
                return null;
            }

            return Nav::fromModel($model);
        });
    }

    public function findByHandle($handle): ?Nav
    {
        return $this->find($handle);
    }

    public function save($nav)
    {
        $model = $nav->toModel();

        $model->save();

        $nav->model($model->fresh());
    }

    public function delete($nav)
    {
        $nav->toModel()->delete();
    }
}
