@extends('layouts.admin.app')

@section('title', 'Precios por Categoría')

@section('content')
<div class="content container-fluid">

    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-tag-outlined" style="font-size:22px"></i></span>
            <span>Precios de Envío por Categoría</span>
        </h1>
        <small class="text-muted">Fórmula: Precio = (Base + Km × Tarifa/km) × Multiplicador dinámico</small>
    </div>

    <div class="row g-3">
        {{-- FORMULARIO NUEVO --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">Nueva categoría de precio</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.pricing.category.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="input-label">Slug (único, sin espacios)</label>
                            <input type="text" name="category_slug" class="form-control" placeholder="restaurants" required>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Nombre de Categoría</label>
                            <input type="text" name="category_name" class="form-control" placeholder="Restaurantes / Comida" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio Base (L)</label>
                                    <input type="number" name="base_price" class="form-control" value="25" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio por Km (L)</label>
                                    <input type="number" name="price_per_km" class="form-control" value="8" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Comisión Zarpya al comercio (%)</label>
                            <input type="number" name="commission_percent" class="form-control" value="15" step="0.01" min="0" max="100" required>
                        </div>
                        <hr>
                        <p class="font-weight-bold text-muted mb-2 small">Distribución por envío (debe sumar 100%)</p>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Repartidor %</label>
                                    <input type="number" name="driver_percent" class="form-control dist-field" value="88" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Zarpya %</label>
                                    <input type="number" name="platform_percent" class="form-control dist-field" value="10" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Seguro %</label>
                                    <input type="number" name="insurance_percent" class="form-control dist-field" value="2" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <small id="dist-total" class="text-muted">Total: 100%</small>
                        <button type="submit" class="btn btn-primary btn-block mt-3">Guardar categoría</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categorías configuradas <span class="badge badge-soft-dark ml-2">{{ $pricings->total() }}</span></h5>
                    <form method="GET" class="ml-auto" style="min-width:220px">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar..." value="{{ $search }}">
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-nowrap table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Categoría</th>
                                <th>Comisión</th>
                                <th>Base</th>
                                <th>/Km</th>
                                <th>3km</th>
                                <th>5km</th>
                                <th>8km</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pricings as $p)
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $p->category_name }}</span>
                                    <br><small class="text-muted">{{ $p->category_slug }}</small>
                                </td>
                                <td>{{ $p->commission_percent }}%</td>
                                <td>L{{ number_format($p->base_price, 0) }}</td>
                                <td>L{{ number_format($p->price_per_km, 0) }}</td>
                                <td class="text-info">L{{ number_format($p->rawPrice(3), 0) }}</td>
                                <td class="text-info">L{{ number_format($p->rawPrice(5), 0) }}</td>
                                <td class="text-info">L{{ number_format($p->rawPrice(8), 0) }}</td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input status-toggle"
                                            data-url="{{ route('admin.zarpya.pricing.category.status') }}"
                                            data-id="{{ $p->id }}"
                                            {{ $p->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </td>
                                <td>
                                    <a href="{{ route('admin.zarpya.pricing.category.edit', $p->id) }}"
                                        class="btn btn-sm btn-outline-primary"><i class="tio-edit"></i></a>
                                    <form action="{{ route('admin.zarpya.pricing.category.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="tio-delete"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $pricings->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Calcula total distribucion en tiempo real
document.querySelectorAll('.dist-field').forEach(el => {
    el.addEventListener('input', () => {
        const fields = Array.from(document.querySelectorAll('.dist-field'));
        const total  = fields.reduce((sum, f) => sum + (parseFloat(f.value) || 0), 0);
        const label  = document.getElementById('dist-total');
        label.textContent = 'Total: ' + total.toFixed(2) + '%';
        label.className   = Math.abs(total - 100) < 0.01 ? 'text-success small' : 'text-danger small';
    });
});

// Toggle status
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
