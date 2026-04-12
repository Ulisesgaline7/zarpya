<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ServiceAdminController extends Controller
{
    // ---------------------------------------------------------------
    // Categorias de servicio
    // ---------------------------------------------------------------

    public function categories()
    {
        $categories = ServiceCategory::withCount('providers')->orderBy('sort_order')->get();
        return view('admin-views.zarpya-services.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'slug'                => 'required|string|max:60|unique:service_categories,slug',
            'name'                => 'required|string|max:120',
            'icon'                => 'nullable|string|max:10',
            'platform_commission' => 'required|numeric|min:0|max:100',
        ]);

        ServiceCategory::create($request->only(['slug', 'name', 'icon', 'platform_commission'])
            + ['status' => true, 'sort_order' => ServiceCategory::max('sort_order') + 1]);

        Toastr::success('Categoría de servicio creada.');
        return back();
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = ServiceCategory::findOrFail($id);
        $request->validate([
            'name'                => 'required|string|max:120',
            'icon'                => 'nullable|string|max:10',
            'platform_commission' => 'required|numeric|min:0|max:100',
        ]);
        $cat->update($request->only(['name', 'icon', 'platform_commission']));
        Toastr::success('Categoría actualizada.');
        return back();
    }

    public function categoryStatus(Request $request)
    {
        ServiceCategory::findOrFail($request->id)->update(['status' => $request->status]);
        Toastr::success('Estado actualizado.');
        return back();
    }

    // ---------------------------------------------------------------
    // Proveedores
    // ---------------------------------------------------------------

    public function providers(Request $request)
    {
        $status     = $request->status ?? 'pending';
        $categoryId = $request->category_id;
        $search     = $request->search;

        $providers = ServiceProvider::with(['user', 'category', 'zone'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, fn ($q) => $q->where('business_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(config('default_pagination'));

        $categories = ServiceCategory::active()->get();
        $stats = [
            'pending'   => ServiceProvider::where('status', 'pending')->count(),
            'active'    => ServiceProvider::where('status', 'active')->count(),
            'suspended' => ServiceProvider::where('status', 'suspended')->count(),
        ];

        return view('admin-views.zarpya-services.providers.index', compact(
            'providers', 'categories', 'stats', 'status', 'categoryId', 'search'
        ));
    }

    public function showProvider($id)
    {
        $provider = ServiceProvider::with(['user', 'category', 'zone', 'serviceRequests'])->findOrFail($id);
        return view('admin-views.zarpya-services.providers.show', compact('provider'));
    }

    public function approveProvider(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:active,suspended,inactive']);
        $provider = ServiceProvider::findOrFail($id);
        $provider->update([
            'status'   => $request->status,
            'verified' => $request->status === 'active',
        ]);
        Toastr::success('Estado del proveedor actualizado.');
        return back();
    }

    public function toggleFeatured(Request $request)
    {
        $provider = ServiceProvider::findOrFail($request->id);
        $provider->update(['featured' => $request->featured]);
        Toastr::success('Destacado actualizado.');
        return back();
    }

    // ---------------------------------------------------------------
    // Solicitudes
    // ---------------------------------------------------------------

    public function requests(Request $request)
    {
        $status = $request->status;

        $requests = ServiceRequest::with(['customer', 'provider', 'category'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(config('default_pagination'));

        $stats = [
            'open'      => ServiceRequest::where('status', 'open')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'disputed'  => ServiceRequest::where('status', 'disputed')->count(),
            'revenue'   => ServiceRequest::where('status', 'completed')->sum('platform_fee'),
        ];

        return view('admin-views.zarpya-services.requests.index', compact('requests', 'stats', 'status'));
    }
}
