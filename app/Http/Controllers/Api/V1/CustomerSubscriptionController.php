<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\CustomerSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSubscriptionController extends Controller
{
    /**
     * GET /api/v1/customer/subscription/plans
     * Devuelve los 3 planes con sus precios y beneficios actuales.
     */
    public function plans(): JsonResponse
    {
        $settings = BusinessSetting::whereIn('key', [
            'sub_plus_price', 'sub_plus_delivery_threshold', 'sub_plus_discount',
            'sub_plus_free_deliveries', 'sub_premium_price', 'sub_premium_discount',
            'sub_premium_cashback',
        ])->pluck('value', 'key');

        return response()->json([
            'plans' => [
                [
                    'type'    => 'free',
                    'name'    => 'Free',
                    'price'   => 0,
                    'period'  => 'para siempre',
                    'benefits'=> [
                        'delivery_free_threshold'  => null,
                        'discount_percentage'      => 0,
                        'monthly_free_deliveries'  => 0,
                        'cashback_percentage'       => 0,
                    ],
                ],
                [
                    'type'    => 'plus',
                    'name'    => 'Plus',
                    'price'   => (float) ($settings['sub_plus_price'] ?? 99),
                    'period'  => '/mes',
                    'benefits'=> [
                        'delivery_free_threshold'  => (float) ($settings['sub_plus_delivery_threshold'] ?? 150),
                        'discount_percentage'      => (float) ($settings['sub_plus_discount'] ?? 5),
                        'monthly_free_deliveries'  => (int)   ($settings['sub_plus_free_deliveries'] ?? 1),
                        'cashback_percentage'       => 0,
                    ],
                ],
                [
                    'type'    => 'premium',
                    'name'    => 'Premium',
                    'price'   => (float) ($settings['sub_premium_price'] ?? 199),
                    'period'  => '/mes',
                    'benefits'=> [
                        'delivery_free_threshold'  => 0,
                        'discount_percentage'      => (float) ($settings['sub_premium_discount'] ?? 10),
                        'monthly_free_deliveries'  => -1, // ilimitado
                        'cashback_percentage'       => (float) ($settings['sub_premium_cashback'] ?? 2),
                    ],
                ],
            ],
        ]);
    }

    /**
     * GET /api/v1/customer/subscription
     * Suscripción activa del cliente autenticado.
     */
    public function current(): JsonResponse
    {
        $user = Auth::user();
        $sub  = $user->active_subscription();

        return response()->json([
            'subscription' => $sub ? [
                'type'       => $sub->type,
                'expires_at' => $sub->expires_at?->toDateTimeString(),
                'is_active'  => $sub->isActive(),
                'benefits'   => $sub->getBenefits(),
            ] : null,
        ]);
    }

    /**
     * POST /api/v1/customer/subscription/subscribe
     * Suscribe al cliente a un plan.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan_type'      => 'required|in:plus,premium',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();

        // Verificar saldo de wallet si paga con wallet
        if ($request->payment_method === 'wallet') {
            $settings = BusinessSetting::whereIn('key', ['sub_plus_price', 'sub_premium_price'])
                ->pluck('value', 'key');
            $price = $request->plan_type === 'plus'
                ? (float) ($settings['sub_plus_price'] ?? 99)
                : (float) ($settings['sub_premium_price'] ?? 199);

            if ($user->wallet_balance < $price) {
                return response()->json([
                    'errors' => [['code' => 'insufficient_balance', 'message' => 'Saldo insuficiente en el monedero.']],
                ], 400);
            }

            // Descontar del wallet
            $user->decrement('wallet_balance', $price);
        }

        // Crear o actualizar suscripción
        CustomerSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'type'       => $request->plan_type,
                'expires_at' => now()->addMonth(),
                'status'     => 1,
            ]
        );

        return response()->json([
            'message'      => "Plan {$request->plan_type} activado correctamente.",
            'subscription' => [
                'type'       => $request->plan_type,
                'expires_at' => now()->addMonth()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * POST /api/v1/customer/subscription/cancel
     */
    public function cancel(): JsonResponse
    {
        $user = Auth::user();
        CustomerSubscription::where('user_id', $user->id)->update(['status' => 0]);

        return response()->json(['message' => 'Suscripción cancelada.']);
    }
}
