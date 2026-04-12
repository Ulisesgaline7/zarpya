@extends('layouts.admin.app')
@section('title', 'Viajes Taxi')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-car" style="font-size:22px"></i></span>
            <span>Viajes Taxi</span>
        </h1>
        <a href="{{ route('admin.zarpya.taxi.rates') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-settings"></i> Configurar tarifas
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-dark">{{ number_format($stats['total']) }}</div>
                <small class="text-muted">Total viajes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-success">{{ number_format($stats['completed']) }}</div>
                <small class="text-muted">Completados</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-primary">{{ number_format($stats['active']) }}</div>
                <small class="text-muted">En curso</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-warning">L{{ number_format($stats['revenue'], 2) }}</div>
                <small class="text-muted">Ingresos Zarpya</small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="GET" class="d-flex gap-2 w-100">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ID de viaje..." value="{{ $search }}" style="max-width:140px">
                <select name="status" class="form-control form-control-sm" style="max-width:160px" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    @foreach(['searching','accepted','arriving','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary">Buscar</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Conductor</th>
                        <th>Origen → Destino</th>
                        <th>Km</th>
                        <th>Total</th>
                        <th>Mult.</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rides as $ride)
                    <tr>
                        <td>#{{ $ride->id }}</td>
                        <td>{{ $ride->customer?->f_name }} {{ $ride->customer?->l_name }}</td>
                        <td>{{ $ride->driver ? $ride->driver->f_name . ' ' . $ride->driver->l_name : '—' }}</td>
                        <td style="max-width:200px">
                            <div class="text-truncate" title="{{ $ride->pickup_address }}">{{ Str::limit($ride->pickup_address, 25) }}</div>
                            <div class="text-muted text-truncate small" title="{{ $ride->dropoff_address }}">→ {{ Str::limit($ride->dropoff_address, 25) }}</div>
                        </td>
                        <td>{{ $ride->distance_km }} km</td>
                        <td class="text-success font-weight-bold">L{{ number_format($ride->total_fare, 2) }}</td>
                        <td><span class="badge badge-soft-warning">×{{ $ride->dynamic_multiplier }}</span></td>
                        <td>
                            @php $statusColors = ['searching'=>'secondary','accepted'=>'info','arriving'=>'primary','in_progress'=>'warning','completed'=>'success','cancelled'=>'danger'] @endphp
                            <span class="badge badge-{{ $statusColors[$ride->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $ride->status)) }}
                            </span>
                        </td>
                        <td><small>{{ $ride->created_at->format('d/m H:i') }}</small></td>
                        <td>
                            <a href="{{ route('admin.zarpya.taxi.ride.show', $ride->id) }}"
                                class="btn btn-xs btn-outline-primary">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $rides->links() }}</div>
    </div>
</div>
@endsection
