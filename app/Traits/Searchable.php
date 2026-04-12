<?php

namespace App\Traits;

use App\Services\AlgoliaService;

trait Searchable
{
    public static function bootSearchable()
    {
        static::saved(function ($model) {
            app(AlgoliaService::class)->indexRecord($model->getTable(), $model->toSearchableArray());
        });

        static::deleted(function ($model) {
            app(AlgoliaService::class)->deleteRecord($model->getTable(), $model->id);
        });
    }

    public function toSearchableArray()
    {
        return $this->toArray();
    }
}
