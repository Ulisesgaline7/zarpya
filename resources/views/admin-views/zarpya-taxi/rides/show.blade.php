@extends('layouts.admin.app')
@section('title', 'Viaje Taxi #' . $ride->id)

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-car" style="font-size:22px"></i></span>
            <span>Viaje Taxi #{{ $ride->id }}</span>
        </h1>
        <a href="{{ route('admin.zarpya.taxi.rides') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-arrow-backward"></i> Volver
        </a>
    </div>

    <div class="row g-3">
        {{-- Info Viaje --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Información del Viaje</h5></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Estado:</strong> 
                        @php $colors = ['pending'=>'warning','accepted'=>'info','driver_assigned'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger'] @endphp
                        <span class="badge badge-{{ $colors[$ride->status] ?? 'secondary' }}">{{ ucfirst($ride->status) }}</span>
                    </p>
                    <p class="mb-1"><strong>Zona:</strong> {{ $ride->zone?->name ?? '—' }}</p>
                    <p class="mb-1"><strong>Tipo vehículo:</strong> {{ ucfirst($ride->vehicle_type) }}</p>
                    <hr>
                    <p class="mb-1"><strong>Origen:</strong> {{ $ride->pickup_address }}</p>
                    <p class="mb-0"><strong>Destino:</strong> {{ $ride->dropoff_address }}</p>
                </div>
            </div>
        </div>

        {{-- Cliente --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Cliente</h5></div>
                <div class="card-body text-center">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px">
                        <i class="tio-user" style="font-size:1.5rem"></i>
                    </div>
                    <h6>{{ $ride->customer?->name ?? '—' }}</h6>
                    <p class="text-muted mb-0">{{ $ride->customer?->phone }}</p>
                </div>
            </div>
        </div>

        {{-- Conductor --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Conductor</h5></div>
                <div class="card-body text-center">
                    @if($ride->driver)
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px">
                            <i class="tio-user" style="font-size:1.5rem"></i>
                        </div>
                        <h6>{{ $ride->driver->name }}</h6>
                        <p class="text-muted mb-0">{{ $ride->driver->phone }}</p>
                    @else
                        <p class="text-muted">Sin conductor asignado</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detalles Financieros --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Detalles del Viaje</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Distancia</p>
                            <h4>{{ number_format($ride->distance, 1) }} km</h4>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Duración</p>
                            <h4>{{ $ride->duration ? floor($ride->duration / 60) . ' min' : '—' }}</h4>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Tarifa Base</p>
                            <h4>L{{ number_format($ride->base_fare, 2) }}</h4>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 text-muted">Total</p>
                            <h4 class="text-success">L{{ number_format($ride->total_fare, 2) }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1 text-muted">Comisión Plataforma</p>
                            <h5>L{{ number_format($ride->platform_earning, 2) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted">Comisión Conductor</p>
                            <h5>L{{ number_format($ride->driver_earning, 2) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1 text-muted">Descuento</p>
                            <h5 class="text-danger">-L{{ number_format($ride->discount_amount ?? 0, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection