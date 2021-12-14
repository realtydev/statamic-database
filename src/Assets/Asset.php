<?php

namespace Realtydev\StatamicDatabase\Assets;

use Illuminate\Support\Facades\Cache;
use Statamic\Assets\Asset as FileAsset;
use Statamic\Facades\Blink;
use Statamic\Support\Arr;

class Asset extends FileAsset
{
    public function meta($key = null)
    {
        if (func_num_args() === 1) {
            return $this->metaValue($key);
        }

        if (! config('statamic.assets.cache_meta')) {
            return $this->generateMeta();
        }

        if ($this->meta) {
            return array_merge($this->meta, ['data' => $this->data->all()]);
        }

        return $this->meta = Cache::rememberForever($this->metaCacheKey(), function () {
            if ($model = AssetModel::where('handle', $this->container()->handle().'::'.$this->metaPath())->first()) {
                return $model->data;
            }
            
            $this->writeMeta($meta = $this->generateMeta());

            return $meta;
        });
    }

    public function exists()
    {
        $files = Blink::once($this->container()->handle().'::files', function () {
            return $this->container()->files();
        });

        if (! $path = $this->path()) {
            return false;
        }

        return $files->contains($path);
    }

    private function metaValue($key)
    {
        $value = Arr::get($this->meta(), $key);

        if (! is_null($value)) {
            return $value;
        }

        Cache::forget($this->metaCacheKey());

        $this->writeMeta($meta = $this->generateMeta());

        return Arr::get($meta, $key);
    }

    public function writeMeta($meta)
    {
        $meta['data'] = Arr::removeNullValues($meta['data']);

        $model = AssetModel::firstOrNew([
            'handle' => $this->container()->handle().'::'.$this->metaPath()
        ]);

        $model->data = $meta;
        
        $model->save();
    }
}