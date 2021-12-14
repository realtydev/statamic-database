<?php

namespace Realtydev\StatamicDatabase\Fieldsets\Traits;

use Realtydev\StatamicDatabase\Fieldsets\FieldsetModel;

trait ExistsAsModel
{
    public function updateModel($fieldset) {
        $model = FieldsetModel::firstOrNew([
            'handle' => $fieldset->handle(),
        ]);
        $model->data = $fieldset->contents();
        $model->save();
    }

    public function deleteModel($blueprint) {
        $model = FieldsetModel::where('handle', $blueprint->handle())->first();
        $model->delete();
    }
}
