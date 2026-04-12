<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\WhatsappNotificationService;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceProviderController extends Controller
{
    /**
     * GET /api/v1/services/categories
     */
    public function categories(): JsonResponse
    {
        $categories = ServiceCategory::active()->get();

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }

    /**
     * GET /api/v1/services/providers
     * Lista proveedores por categoria y zona.
     */
    public function providers(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:service_categories,id',
            'zone_id'     => 'nullable|integer|exists:zones,id',
        ]);

        $providers = ServiceProvider::active()
            ->with(['category', 'zone'])
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->zone_id,     fn ($q) => $q->where('zone_id', $request->zone_id))
            ->orderByDesc('featured')
            ->orderByDesc('avg_rating')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data'   => $providers,
        ]);
    }

    /**
     * GET /api/v1/services/providers/{id}
     */
    public function showProvider(int $id): JsonResponse
    {
        $provider = ServiceProvider::active()
            ->with(['category', 'zone', 'user'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $provider,
        ]);
    }

    /**
     * POST /api/v1/services/request
     * Crea una solicitud de servicio.
     */
    public function createRequest(Request $request): JsonResponse
    {
        $request->validate([
            'category_id'  => 'required|integer|exists:service_categories,id',
            'description'  => 'required|string|max:1000',
            'address'      => 'required|string|max:255',
            'lat'          => 'nullable|numeric',
            'lng'          => 'nullable|numeric',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $serviceRequest = ServiceRequest::create([
            'customer_id'  => Auth::id(),
            'category_id'  => $request->category_id,
            'description'  => $request->description,
            'address'      => $request->address,
            'lat'          => $request->lat,
            'lng'          => $request->lng,
            'scheduled_at' => $request->scheduled_at,
            'status'       => 'open',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Solicitud enviada. Los proveedores cercanos serán notificados.',
            'data'    => $serviceRequest->load('category'),
        ], 201);
    }

    /**
     * POST /api/v1/services/request/{id}/accept-quote
     * El cliente acepta la cotizacion del proveedor.
     */
    public function acceptQuote(int $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::where('customer_id', Auth::id())
            ->where('status', 'quoted')
            ->findOrFail($id);

        $serviceRequest->update(['status' => 'accepted']);

        return response()->json([
            'status'  => true,
            'message' => 'Cotizacion aceptada. El proveedor será notificado.',
            'data'    => $serviceRequest,
        ]);
    }

    /**
     * POST /api/v1/services/request/{id}/complete
     * Marca el servicio como completado y deja calificacion.
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $serviceRequest = ServiceRequest::where('customer_id', Auth::id())
            ->where('status', 'in_progress')
            ->with('provider')
            ->findOrFail($id);

        $finalPrice      = $serviceRequest->final_price ?? $serviceRequest->quoted_price;
        $category        = $serviceRequest->category;
        $commissionPct   = $category?->platform_commission ?? 15.00;
        $platformFee     = round($finalPrice * $commissionPct / 100, 2);
        $providerEarning = round($finalPrice - $platformFee, 2);

        $serviceRequest->update([
            'status'           => 'completed',
            'rating'           => $request->rating,
            'review'           => $request->review,
            'platform_fee'     => $platformFee,
            'provider_earning' => $providerEarning,
            'paid'             => true,
        ]);

        // Actualizar promedio del proveedor
        $provider = $serviceRequest->provider;
        if ($provider) {
            $newTotal  = $provider->total_reviews + 1;
            $newRating = round(
                (($provider->avg_rating * $provider->total_reviews) + $request->rating) / $newTotal,
                2
            );
            $provider->update([
                'avg_rating'    => $newRating,
                'total_reviews' => $newTotal,
                'total_jobs'    => $provider->total_jobs + 1,
            ]);

            // Notificar al proveedor por WhatsApp
            $providerUser = $provider->user;
            if ($providerUser?->phone) {
                WhatsappNotificationService::send(
                    phone:          $providerUser->phone,
                    templateName:   WhatsappNotificationService::TEMPLATE_SERVICE_COMPLETED,
                    params:         [number_format($providerEarning, 2), $providerUser->f_name ?? 'Proveedor'],
                    notifiableType: ServiceRequest::class,
                    notifiableId:   $serviceRequest->id
                );
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Servicio completado y calificado.',
            'data'    => $serviceRequest,
        ]);
    }

    /**
     * GET /api/v1/services/my-requests
     */
    public function myRequests(): JsonResponse
    {
        $requests = ServiceRequest::where('customer_id', Auth::id())
            ->with(['category', 'provider'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'data'   => $requests,
        ]);
    }
}
