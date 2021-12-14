<?php

namespace Realtydev\StatamicDatabase\Blueprints;

use Realtydev\StatamicDatabase\Blueprints\Traits\ExistsAsModel;
use Realtydev\StatamicDatabase\Exceptions\DefaultBlueprintNotFoundException;
use Statamic\Facades\Blink;
use Statamic\Fields\Blueprint;
use Statamic\Fields\BlueprintRepository as BaseBlueprintRepository;
use Statamic\Support\Arr;

class BlueprintRepository extends BaseBlueprintRepository
{

    use ExistsAsModel;

    private const BLINK_FOUND = 'blueprints.found';
    private const BLINK_FROM_FILE = 'blueprints.from-file';
    private const BLINK_NAMESPACE_PATHS = 'blueprints.paths-in-namespace';

    public function find($blueprint): ?Blueprint
    {
        return Blink::store(self::BLINK_FOUND)->once($blueprint, function () use ($blueprint) {
            [$namespace, $handle] = $this->getNamespaceAndHandle($blueprint);
            if (! $blueprint) {
                return null;
            }

            if (($blueprintModel = BlueprintModel::where('namespace', $namespace)->where('handle', $handle)->first()) === null) {
                throw_if(
                    $namespace === null && $handle === 'default',
                    DefaultBlueprintNotFoundException::class,
                    'Default Blueprint is required but not found. '
                );

                return null;
            }

            return $this->makeBlueprintFromModel($blueprintModel) ?? $this->findFallback($blueprint);
        });
    }

    public function save(Blueprint $blueprint)
    {
        $this->clearBlinkCaches();

        $this->updateModel($blueprint);
    }

    public function delete(Blueprint $blueprint)
    {
        $this->clearBlinkCaches();

        $this->deleteModel($blueprint);
    }

    private function clearBlinkCaches()
    {
        Blink::store(self::BLINK_FOUND)->flush();
        Blink::store(self::BLINK_FROM_FILE)->flush();
        Blink::store(self::BLINK_NAMESPACE_PATHS)->flush();
    }

    public function in(string $namespace)
    {
        return $this
            ->filesIn($namespace)
            ->map(function ($file) {
                return $this->makeBlueprintFromModel($file);
            })
            ->sort(function ($a, $b) {
                $orderA = $a->order() ?? 99999;
                $orderB = $b->order() ?? 99999;

                return $orderA === $orderB
                    ? $a->title() <=> $b->title()
                    : $orderA <=> $orderB;
            })
            ->keyBy->handle();
    }

    private function filesIn($namespace)
    {
        return Blink::store(self::BLINK_NAMESPACE_PATHS)->once($namespace, function () use ($namespace) {
            $namespace = str_replace('/', '.', $namespace);

            if (count(($blueprintModels = BlueprintModel::where('namespace', $namespace)->get())) == 0) {
                return collect();
            }

            return $blueprintModels;
        });
    }

    private function makeBlueprintFromModel($model)
    {
        return Blink::store(self::BLINK_FROM_FILE)->once('database:blueprints:' . $model->id, function () use ($model) {
            return (new Blueprint)
                ->setHidden(Arr::get($model->data, 'hide'))
                ->setOrder(Arr::get($model->data, 'order'))
                ->setHandle($model->handle)
                ->setNamespace($model->namespace)
                ->setContents($model->data);
        });
    }

    private function getNamespaceAndHandle($blueprint)
    {
        $blueprint = str_replace('/', '.', $blueprint);
        $parts = explode('.', $blueprint);
        $handle = array_pop($parts);
        $namespace = implode('.', $parts);
        $namespace = empty($namespace) ? null : $namespace;

        return [$namespace, $handle];
    }
}
