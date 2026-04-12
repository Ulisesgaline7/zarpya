@extends('layouts.admin.app')
@section('title', 'Editar precio - ' . $pricing->category_name)

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <a href="{{ route('admin.zarpya.pricing.categories') }}" class="btn btn-sm btn-outline-secondary mr-2"><i class="tio-arrow-backward"></i></a>
            Editar: {{ $pricing->category_name }}
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.pricing.category.update', $pricing->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label class="input-label">Slug</label>
                            <input type="text" name="category_slug" class="form-control" value="{{ $pricing->category_slug }}" required>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Nombre</label>
                            <input type="text" name="category_name" class="form-control" value="{{ $pricing->category_name }}" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio Base (L)</label>
                                    <input type="number" name="base_price" class="form-control" value="{{ $pricing->base_price }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio por Km (L)</label>
                                    <input type="number" name="price_per_km" class="form-control" value="{{ $pricing->price_per_km }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Comisión al comercio (%)</label>
                            <input type="number" name="commission_percent" class="form-control" value="{{ $pricing->commission_percent }}" step="0.01" min="0" max="100" required>
                        </div>

                        {{-- Preview tabla de precios --}}
                        <div class="alert alert-soft-info p-3 mb-3">
                            <p class="mb-1 font-weight-bold">Preview (sin multiplicador)</p>
                            <div class="row text-center">
                                @foreach([3,5,8] as $km)
                                <div class="col-4">
                                    <div class="text-muted small">{{ $km }} km</div>
                                    <strong class="text-info" id="price_{{ $km }}km">L{{ number_format($pricing->rawPrice($km),2) }}</strong>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>
                        <p class="font-weight-bold text-muted mb-2 small">Distribución por envío (debe sumar 100%)</p>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Repartidor %</label>
                                    <input type="number" name="driver_percent" class="form-control dist-field" value="{{ $pricing->driver_percent }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Zarpya %</label>
                                    <input type="number" name="platform_percent" class="form-control dist-field" value="{{ $pricing->platform_percent }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Seguro %</label>
                                    <input type="number" name="insurance_percent" class="form-control dist-field" value="{{ $pricing->insurance_percent }}" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <small id="dist-total" class="text-success small">Total: {{ $pricing->driver_percent + $pricing->platform_percent + $pricing->insurance_percent }}%</small>

                        <button type="submit" class="btn btn-primary btn-block mt-3">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.dist-field').forEach(el => {
    el.addEventListener('input', () => {
        const fields = Array.from(document.querySelectorAll('.dist-field'));
        const total  = fields.reduce((sum, f) => sum + (parseFloat(f.value) || 0), 0);
        const label  = document.getElementById('dist-total');
        label.textContent = 'Total: ' + total.toFixed(2) + '%';
        label.className   = Math.abs(total - 100) < 0.01 ? 'text-success small' : 'text-danger small';
    });
});
</script>
@endpush
