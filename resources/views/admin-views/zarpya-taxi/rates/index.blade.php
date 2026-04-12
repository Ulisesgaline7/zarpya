@extends('layouts.admin.app')
@section('title', 'Tarifas Taxi por Zona')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-car" style="font-size:22px"></i></span>
            <span>Módulo Taxi — Tarifas por Zona</span>
        </h1>
        <a href="{{ route('admin.zarpya.taxi.rides') }}" class="btn btn-sm btn-outline-primary ml-auto">
            <i class="tio-list"></i> Ver viajes
        </a>
    </div>

    <div class="row g-3">
        {{-- FORM --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Nueva tarifa</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.taxi.rate.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="input-label">Zona</label>
                            <select name="zone_id" class="form-control js-select2-custom" required>
                                <option value="">Seleccionar zona...</option>
                                @foreach($zones as $z)
                                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Tipo de vehículo</label>
                            <select name="vehicle_type" class="form-control" required>
                                <option value="standard">Estándar</option>
                                <option value="premium">Premium</option>
                                <option value="moto">Moto</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Tarifa base (L)</label>
                                    <input type="number" name="base_fare" class="form-control" value="30" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Por km (L)</label>
                                    <input type="number" name="fare_per_km" class="form-control" value="12" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Por minuto (L)</label>
                                    <input type="number" name="fare_per_min" class="form-control" value="2" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Mínimo (L)</label>
                                    <input type="number" name="min_fare" class="form-control" value="40" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Comisión Zarpya (%)</label>
                            <input type="number" name="platform_percent" class="form-control" value="15" step="0.01" min="0" max="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Guardar tarifa</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tarifas configuradas</h5>
                    <form method="GET" class="ml-auto">
                        <select name="zone_id" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">Todas las zonas</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}" {{ $zoneId == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Zona</th>
                                <th>Tipo</th>
                                <th>Base</th>
                                <th>/km</th>
                                <th>/min</th>
                                <th>Mínimo</th>
                                <th>Comisión</th>
                                <th>Est.</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rates as $rate)
                            <tr>
                                <td>{{ $rate->zone?->name ?? '—' }}</td>
                                <td><span class="badge badge-soft-info">{{ ucfirst($rate->vehicle_type) }}</span></td>
                                <td>L{{ number_format($rate->base_fare, 2) }}</td>
                                <td>L{{ number_format($rate->fare_per_km, 2) }}</td>
                                <td>L{{ number_format($rate->fare_per_min, 2) }}</td>
                                <td>L{{ number_format($rate->min_fare, 2) }}</td>
                                <td>{{ $rate->platform_percent }}%</td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input status-toggle"
                                            data-url="{{ route('admin.zarpya.taxi.rate.status') }}"
                                            data-id="{{ $rate->id }}"
                                            {{ $rate->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </td>
                                <td>
                                    <form action="{{ route('admin.zarpya.taxi.rate.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="tio-delete"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $rates->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        fetch(this.dataset.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            body: JSON.stringify({ id: this.dataset.id, status: this.checked ? 1 : 0 })
        }).then(r => r.ok ? location.reload() : alert('Error'));
    });
});
</script>
@endpush
