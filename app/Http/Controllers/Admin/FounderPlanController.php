<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class FounderPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPackage::whereIn('founder_type', ['pionero', 'elite', 'boost', 'standard'])
            ->orderByRaw("FIELD(founder_type, 'pionero', 'elite', 'boost', 'standard')")
            ->get();

        return view('admin-views.founder-plans.index', compact('plans'));
    }

    /**
     * Abrir o cerrar cupos de un plan específico.
     */
    public function toggleSlots(int $id)
    {
        $plan = SubscriptionPackage::findOrFail($id);
        $plan->update(['slots_open' => ! $plan->slots_open]);

        $status = $plan->slots_open ? 'abiertos' : 'cerrados';
        Toastr::success("Cupos del plan {$plan->package_name} {$status}.");
        return back();
    }

    /**
     * Cerrar todos los planes fundadores (inicio de operaciones).
     */
    public function closeAll()
    {
        SubscriptionPackage::where('is_founder', true)->update(['slots_open' => false]);
        Toastr::success('Todos los planes fundadores han sido cerrados. Inicio de operaciones marcado.');
        return back();
    }

    /**
     * Asignar plan fundador a un negocio manualmente.
     */
    public function assignToStore(Request $request)
    {
        $request->validate([
            'store_id'     => 'required|integer|exists:stores,id',
            'founder_type' => 'required|in:pionero,elite,boost',
        ]);

        $plan = SubscriptionPackage::where('founder_type', $request->founder_type)->firstOrFail();

        // Verificar cupos
        if ($plan->max_slots && $plan->used_slots >= $plan->max_slots) {
            Toastr::error("El plan {$plan->package_name} no tiene cupos disponibles.");
            return back();
        }

        if (! $plan->slots_open) {
            Toastr::error("El plan {$plan->package_name} está cerrado.");
            return back();
        }

        // Asignar al store
        $store = \App\Models\Store::findOrFail($request->store_id);
        $store->update([
            'founder_plan'          => $request->founder_type,
            'founder_badge'         => $plan->badge_label,
            'founder_active'        => true,
            'comission'             => $plan->commission_percent,
            'store_business_model'  => 'subscription',
            'package_id'            => $plan->id,
        ]);

        // Incrementar cupos usados
        $plan->increment('used_slots');

        // Crear StoreSubscription — el observer se encargará de los créditos Boost
        \App\Models\StoreSubscription::updateOrCreate(
            ['store_id' => $store->id],
            [
                'package_id'   => $plan->id,
                'expiry_date'  => now()->addYears(100)->toDateString(), // permanente
                'validity'     => $plan->validity,
                'max_order'    => $plan->max_order,
                'max_product'  => $plan->max_product,
                'pos'          => $plan->pos,
                'mobile_app'   => $plan->mobile_app,
                'chat'         => $plan->chat,
                'review'       => $plan->review,
                'self_delivery'=> $plan->self_delivery,
                'status'       => 1,
                'is_trial'     => 0,
            ]
        );

        Toastr::success("Plan {$plan->package_name} asignado a {$store->name}.");
        return back();
    }
}
