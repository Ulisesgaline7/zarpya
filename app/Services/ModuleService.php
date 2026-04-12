<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class ModuleService
{
    use FileManagerTrait;

    public function getAddData(Object $request): array
    {
        return [
            'module_name' => $request->module_name[array_search('default', $request->lang)],
            'icon' => $this->upload('module/', 'png', $request->file('icon')),
            'thumbnail' => $this->upload('module/', 'png', $request->file('thumbnail')),
            'module_type' => $request->module_type,
            'theme_id' => 1,
            'description' => $request->description[array_search('default', $request->lang)],
            'base_price' => $request->base_price ?? 25,
            'price_per_km' => $request->price_per_km ?? 8,
            'commission_percent' => $request->commission_percent ?? 15,
            'status' => 1,
        ];
    }
    public function getUpdateData(Object $request, object $module): array
    {
        $data = [
            'module_name' => $request->module_name[array_search('default', $request->lang)],
            'icon' => $request->has('icon') ? $this->updateAndUpload('module/', $module->icon, 'png', $request->file('icon')) : $module->icon,
            'thumbnail' => $request->has('thumbnail') ? $this->updateAndUpload('module/', $module->thumbnail, 'png', $request->file('thumbnail')) : $module->thumbnail,
            'theme_id' => 1,
            'description' => $request->description[array_search('default', $request->lang)],
            'all_zone_service' => false,
        ];

        // Agregar campos de precio si existen
        if ($request->has('base_price')) {
            $data['base_price'] = $request->base_price;
        }
        if ($request->has('price_per_km')) {
            $data['price_per_km'] = $request->price_per_km;
        }
        if ($request->has('price_per_minute')) {
            $data['price_per_minute'] = $request->price_per_minute;
        }
        if ($request->has('minimum_fare')) {
            $data['minimum_fare'] = $request->minimum_fare;
        }
        if ($request->has('deposit')) {
            $data['deposit'] = $request->deposit;
        }
        if ($request->has('commission_percent')) {
            $data['commission_percent'] = $request->commission_percent;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        } else {
            $data['status'] = 0;
        }

        return $data;
    }

    public function getDropdownData(Object $data, object $request): array
    {

        $formattedData = $data->map(function ($condition) {
            return [
                'id' => $condition->id,
                'text' => $condition->name,
            ];
        });


        if(isset($request->all))
        {
            $formattedData[]=(object)['id'=>'all', 'text'=>translate('messages.all')];
        }

        return $formattedData;
    }

}
