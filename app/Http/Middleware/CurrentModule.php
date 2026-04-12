<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use App\Models\Module;

class CurrentModule
{
    public function handle($request, Closure $next)
    {
        // 1. Establecer module_id desde URL o sesión
        if ($request->get('module_id')) {
            session()->put('current_module', $request->get('module_id'));
            Config::set('module.current_module_id', $request->get('module_id'));
        } else {
            Config::set('module.current_module_id', session()->get('current_module'));
        }

        // 2. Resolver el módulo y su type
        $module_id = Config::get('module.current_module_id');
        $module_id = is_array($module_id) ? null : $module_id;
        $module    = $module_id
            ? Module::with('translations')->find($module_id)
            : Module::with('translations')->active()->first();

        if ($module) {
            Config::set('module.current_module_id',   $module->id);
            Config::set('module.current_module_type', $module->module_type);
            Config::set('module.current_module_name', $module->module_name);
        } else {
            Config::set('module.current_module_id',   null);
            Config::set('module.current_module_type', 'settings');
        }

        // 3. Sobrescrituras por sección de URL (mayor prioridad)
        if (Request::is('admin/users*')) {
            Config::set('module.current_module_id',   null);
            Config::set('module.current_module_type', 'users');
        }
        if (Request::is('admin/transactions*')) {
            Config::set('module.current_module_id',   null);
            Config::set('module.current_module_type', 'transactions');
        }
        if (Request::is('admin/dispatch*')) {
            Config::set('module.current_module_id',   null);
            Config::set('module.current_module_type', 'dispatch');
        }
        if (Request::is('admin/business-settings/*') || Request::is('taxvat/*')) {
            Config::set('module.current_module_id',   null);
            Config::set('module.current_module_type', 'settings');
        }

        // 4. Módulos con sidebar propio — forzar type por ruta
        if (Request::is('admin/zarpya/taxi*')) {
            Config::set('module.current_module_id',   46);
            Config::set('module.current_module_type', 'taxi');
        }
        if (Request::is('admin/zarpya/services*')) {
            Config::set('module.current_module_id',   47);
            Config::set('module.current_module_type', 'services');
        }
        if (Request::is('admin/rental*')) {
            Config::set('module.current_module_id',   48);
            Config::set('module.current_module_type', 'rental');
        }

        return $next($request);
    }
}
