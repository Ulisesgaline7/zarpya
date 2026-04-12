@extends('layouts.admin.app')
@section('title', 'Proveedor de Servicio')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-user-star-outlined" style="font-size:22px"></i></span>
            <span>{{ $provider->business_name }}</span>
        </h1>
        <a href="{{ route('admin.zarpya.services.providers') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-arrow-backward"></i> Volver
        </a>
    </div>

    <div class="row g-3">
        {{-- Info Principal --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Información</h5></div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($provider->avatar)
                            <img src="{{ asset('storage/app/public/service-provider/'.$provider->avatar) }}"
                                 class="rounded-circle" style="width:80px;height:80px;object-fit:cover"
                                 onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px">
                                <i class="tio-user-star-outlined" style="font-size:2rem"></i>
                            </div>
                        @endif
                        <h4 class="mt-2 mb-1">{{ $provider->business_name }}</h4>
                        <span class="badge badge-{{ $provider->status === 'active' ? 'success' : ($provider->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($provider->status) }}
                        </span>
                        @if($provider->verified)
                            <span class="badge badge-soft-success ml-1">✓ Verificado</span>
                        @endif
                        @if($provider->featured)
                            <span class="badge badge-soft-warning ml-1">★ Destacado</span>
                        @endif
                    </div>
                    <hr>
                    @if($provider->description)
                        <p class="text-muted small mb-3">{{ $provider->description }}</p>
                    @endif
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><strong>Categoría:</strong> {{ $provider->category?->name ?? '—' }}</li>
                        <li class="mb-1"><strong>Zona:</strong> {{ $provider->zone?->name ?? 'No asignada' }}</li>
                        <li class="mb-1"><strong>Teléfono:</strong> {{ $provider->phone ?? '—' }}</li>
                        <li class="mb-1"><strong>Tarifa/hora:</strong>
                            {{ $provider->hourly_rate ? \App\CentralLogics\Helpers::format_currency($provider->hourly_rate) : '—' }}
                        </li>
                        <li class="mb-1"><strong>Tarifa fija:</strong>
                            {{ $provider->fixed_rate ? \App\CentralLogics\Helpers::format_currency($provider->fixed_rate) : '—' }}
                        </li>
                        <li class="mb-1"><strong>Calificación:</strong>
                            @if($provider->avg_rating > 0)
                                <span class="text-warning">★ {{ number_format($provider->avg_rating, 1) }}</span>
                                <small class="text-muted">({{ $provider->total_reviews }} reseñas)</small>
                            @else
                                <span class="text-muted">Sin calificaciones</span>
                            @endif
                        </li>
                        <li class="mb-0"><strong>Trabajos completados:</strong> {{ $provider->total_jobs }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Acciones</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.services.provider.approve', $provider->id) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="input-label">Cambiar estado</label>
                        <div class="input-group">
                            <select name="status" class="form-control">
                                <option value="active"    {{ $provider->status === 'active'    ? 'selected' : '' }}>Activo</option>
                                <option value="suspended" {{ $provider->status === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                                <option value="inactive"  {{ $provider->status === 'inactive'  ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('admin.zarpya.services.provider.featured') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $provider->id }}">
                        <input type="hidden" name="featured" value="{{ $provider->featured ? 0 : 1 }}">
                        <button class="btn btn-{{ $provider->featured ? 'warning' : 'outline-warning' }} btn-block">
                            {{ $provider->featured ? '★ Quitar de destacados' : '☆ Destacar proveedor' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Usuario vinculado --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Usuario</h5></div>
                <div class="card-body text-center">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px">
                        <i class="tio-user" style="font-size:1.5rem"></i>
                    </div>
                    @if($provider->user)
                        <h6>{{ $provider->user->f_name }} {{ $provider->user->l_name }}</h6>
                        <p class="text-muted mb-1">{{ $provider->user->email ?? '—' }}</p>
                        <p class="text-muted mb-0">{{ $provider->user->phone ?? '—' }}</p>
                        <small class="text-muted">Registrado: {{ $provider->user->created_at->format('d/m/Y') }}</small>
                    @else
                        <p class="text-muted">Sin usuario vinculado</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Solicitudes del proveedor --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Solicitudes recientes</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Cotización</th>
                                <th>Final</th>
                                <th>Ganancia</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($provider->serviceRequests()->with('customer')->latest()->take(20)->get() as $req)
                            @php
                                $colors = [
                                    'open'        => 'info',
                                    'quoted'      => 'warning',
                                    'accepted'    => 'success',
                                    'in_progress' => 'primary',
                                    'completed'   => 'success',
                                    'cancelled'   => 'danger',
                                    'disputed'    => 'danger',
                                ];
                            @endphp
                            <tr>
                                <td>#{{ $req->id }}</td>
                                <td>{{ $req->customer ? $req->customer->f_name . ' ' . $req->customer->l_name : '—' }}</td>
                                <td>{{ Str::limit($req->description, 50) }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $colors[$req->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                    </span>
                                </td>
                                <td>{{ $req->quoted_price ? \App\CentralLogics\Helpers::format_currency($req->quoted_price) : '—' }}</td>
                                <td>{{ $req->final_price ? \App\CentralLogics\Helpers::format_currency($req->final_price) : '—' }}</td>
                                <td class="text-success">{{ $req->provider_earning ? \App\CentralLogics\Helpers::format_currency($req->provider_earning) : '—' }}</td>
                                <td>{{ $req->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">Sin solicitudes aún</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
