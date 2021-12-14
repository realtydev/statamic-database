<?php

namespace Realtydev\StatamicDatabase\Blueprints\Traits;

use Realtydev\StatamicDatabase\Blueprints\BlueprintModel;

trait ExistsAsModel
{
    public function updateModel($blueprint) {
        $model = BlueprintModel::firstOrNew([
            'handle' => $blueprint->handle(),
            'namespace' => $blueprint->namespace() ?? null,
        ]);
        $model->data = $blueprint->contents();
        $model->save();
    }

    public function deleteModel($blueprint) {
        $model = BlueprintModel::where('namespace', $blueprint->namespace() ?? null)->where('handle', $blueprint->handle())->first();
        $model->delete();
    }
}
