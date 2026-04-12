@extends('layouts.admin.app')

@section('title', 'Suscripciones de Clientes')

@push('css_or_js')
<style>
/* ── Tabla comparativa de planes ─────────────────────────── */
.plan-table { border-collapse: separate; border-spacing: 0; width: 100%; }
.plan-table th, .plan-table td {
    padding: 14px 18px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}
.plan-table thead th { background: #f8f9fa; font-weight: 600; font-size: 13px; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }
.plan-table tbody tr:last-child td { border-bottom: none; }
.plan-table tbody tr:hover td { background: #fafafa; }

/* Columnas de plan */
.col-plan { width: 200px; text-align: center; }
.plan-header { padding: 20px 18px 16px; text-align: center; }
.plan-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; }
.plan-free    .plan-badge { background: #f0f0f0; color: #555; }
.plan-plus    .plan-badge { background: #e3f2fd; color: #1565c0; }
.plan-premium .plan-badge { background: #f3e5f5; color: #6a1b9a; }
.plan-price { font-size: 2rem; font-weight: 800; line-height: 1; margin: 8px 0 2px; }
.plan-free    .plan-price { color: #555; }
.plan-plus    .plan-price { color: #1565c0; }
.plan-premium .plan-price { color: #6a1b9a; }
.plan-period { font-size: 12px; color: #aaa; }

/* Check / X */
.check { color: #28a745; font-size: 1.1rem; }
.cross { color: #dc3545; font-size: 1.1rem; }
.feature-label { font-size: 14px; color: #333; }
.feature-sub   { font-size: 12px; color: #999; }

/* Columna destacada (Premium) */
.col-premium { background: linear-gradient(180deg, #f9f0ff 0%, #fff 100%); }
.col-plus    { background: linear-gradient(180deg, #f0f7ff 0%, #fff 100%); }

/* Inputs de configuración */
.config-input { max-width: 120px; text-align: center; }

/* CTA buttons */
.btn-plan-free    { background: #e9ecef; color: #555; border: none; }
.btn-plan-plus    { background: #1565c0; color: #fff; border: none; }
.btn-plan-premium { background: #6a1b9a; color: #fff; border: none; }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    {{-- Encabezado --}}
    <div class="page-header">
        <h1 class="page-header-title mr-3">
            <span class="page-header-icon">
                <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
            </span>
            <span>Configuración del Negocio</span>
        </h1>
        @include('admin-views.business-settings.partials.nav-menu')
    </div>

    {{-- Estadísticas rápidas --}}
    @php
        $totalSubs    = \App\Models\CustomerSubscription::where('status', 1)->count();
        $plusSubs     = \App\Models\CustomerSubscription::where('status', 1)->where('type', 'plus')->count();
        $premiumSubs  = \App\Models\CustomerSubscription::where('status', 1)->where('type', 'premium')->count();
        $freeSubs     = \App\Models\User::count() - $totalSubs;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card text-center py-3 border-0 shadow-sm">
                <div class="h3 mb-0 text-muted fw-bold">{{ number_format($freeSubs) }}</div>
                <small class="text-muted">Clientes en plan Free</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center py-3 border-0 shadow-sm" style="border-top:3px solid #1565c0 !important;">
                <div class="h3 mb-0 fw-bold" style="color:#1565c0;">{{ number_format($plusSubs) }}</div>
                <small class="text-muted">Suscriptores Plus activos</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center py-3 border-0 shadow-sm" style="border-top:3px solid #6a1b9a !important;">
                <div class="h3 mb-0 fw-bold" style="color:#6a1b9a;">{{ number_format($premiumSubs) }}</div>
                <small class="text-muted">Suscriptores Premium activos</small>
            </div>
        </div>
    </div>

    {{-- Tabla comparativa + formulario --}}
    <form action="{{ route('admin.business-settings.update-ad-surge') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Planes de Suscripción para Clientes</h5>
                <small class="text-muted">Configura los precios y beneficios de cada plan. Los cambios aplican inmediatamente.</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="plan-table">
                        <thead>
                            <tr>
                                <th style="width:220px;">Característica</th>
                                <th class="col-plan plan-free text-center">
                                    <div class="plan-header">
                                        <div><span class="plan-badge">⚪ Free</span></div>
                                        <div class="plan-price">L 0</div>
                                        <div class="plan-period">para siempre</div>
                                    </div>
                                </th>
                                <th class="col-plan col-plus text-center">
                                    <div class="plan-header">
                                        <div><span class="plan-badge" style="background:#e3f2fd;color:#1565c0;">🔵 Plus</span></div>
                                        <div class="plan-price" style="color:#1565c0;">
                                            L <span id="plus-price-display">{{ $data['sub_plus_price'] ?? '99' }}</span>
                                        </div>
                                        <div class="plan-period">por mes</div>
                                    </div>
                                </th>
                                <th class="col-plan col-premium text-center">
                                    <div class="plan-header">
                                        <div><span class="plan-badge" style="background:#f3e5f5;color:#6a1b9a;">🟣 Premium</span></div>
                                        <div class="plan-price" style="color:#6a1b9a;">
                                            L <span id="premium-price-display">{{ $data['sub_premium_price'] ?? '199' }}</span>
                                        </div>
                                        <div class="plan-period">por mes</div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- PRECIO --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Precio mensual</div>
                                    <div class="feature-sub">Cobro recurrente en lempiras</div>
                                </td>
                                <td class="text-center text-muted">Gratis</td>
                                <td class="col-plus text-center">
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:140px;margin:auto;">
                                        <div class="input-group-prepend"><span class="input-group-text">L</span></div>
                                        <input type="number" name="sub_plus_price" step="0.01" min="0"
                                               class="form-control config-input"
                                               value="{{ $data['sub_plus_price'] ?? '99.00' }}"
                                               oninput="document.getElementById('plus-price-display').textContent=this.value">
                                    </div>
                                </td>
                                <td class="col-premium text-center">
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:140px;margin:auto;">
                                        <div class="input-group-prepend"><span class="input-group-text">L</span></div>
                                        <input type="number" name="sub_premium_price" step="0.01" min="0"
                                               class="form-control config-input"
                                               value="{{ $data['sub_premium_price'] ?? '199.00' }}"
                                               oninput="document.getElementById('premium-price-display').textContent=this.value">
                                    </div>
                                </td>
                            </tr>

                            {{-- ENVÍOS --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Envío gratis</div>
                                    <div class="feature-sub">Condición para envío sin costo</div>
                                </td>
                                <td class="text-center">
                                    <span class="cross">✗</span>
                                    <div class="feature-sub">Precio normal</div>
                                </td>
                                <td class="col-plus text-center">
                                    <div class="feature-sub mb-1">Gratis en pedidos mayores a:</div>
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:140px;margin:auto;">
                                        <div class="input-group-prepend"><span class="input-group-text">L</span></div>
                                        <input type="number" name="sub_plus_delivery_threshold" step="0.01" min="0"
                                               class="form-control config-input"
                                               value="{{ $data['sub_plus_delivery_threshold'] ?? '150.00' }}">
                                    </div>
                                </td>
                                <td class="col-premium text-center">
                                    <span class="check">✓</span>
                                    <div class="feature-sub">Gratis en <strong>todos</strong> los pedidos</div>
                                </td>
                            </tr>

                            {{-- ENVÍOS GRATIS AL MES --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Envíos gratis al mes</div>
                                    <div class="feature-sub">Cantidad fija sin importar el monto</div>
                                </td>
                                <td class="text-center">
                                    <span class="cross">✗</span>
                                </td>
                                <td class="col-plus text-center">
                                    <input type="number" name="sub_plus_free_deliveries" min="0" max="99"
                                           class="form-control config-input mx-auto"
                                           value="{{ $data['sub_plus_free_deliveries'] ?? '1' }}"
                                           style="max-width:80px; text-align:center;">
                                    <div class="feature-sub mt-1">envío(s) gratis/mes</div>
                                </td>
                                <td class="col-premium text-center">
                                    <span class="check">✓</span>
                                    <div class="feature-sub">Ilimitados</div>
                                </td>
                            </tr>

                            {{-- DESCUENTO --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Descuento en pedidos</div>
                                    <div class="feature-sub">Aplica sobre el subtotal del pedido</div>
                                </td>
                                <td class="text-center">
                                    <span class="cross">✗</span>
                                    <div class="feature-sub">Sin descuento</div>
                                </td>
                                <td class="col-plus text-center">
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:120px;margin:auto;">
                                        <input type="number" name="sub_plus_discount" step="0.1" min="0" max="100"
                                               class="form-control config-input"
                                               value="{{ $data['sub_plus_discount'] ?? '5.0' }}">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="feature-sub mt-1">en todo</div>
                                </td>
                                <td class="col-premium text-center">
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:120px;margin:auto;">
                                        <input type="number" name="sub_premium_discount" step="0.1" min="0" max="100"
                                               class="form-control config-input"
                                               value="{{ $data['sub_premium_discount'] ?? '10.0' }}">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="feature-sub mt-1">en todo</div>
                                </td>
                            </tr>

                            {{-- CASHBACK --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Cashback extra</div>
                                    <div class="feature-sub">Se acredita al monedero del cliente</div>
                                </td>
                                <td class="text-center">
                                    <span class="cross">✗</span>
                                </td>
                                <td class="col-plus text-center">
                                    <span class="cross">✗</span>
                                </td>
                                <td class="col-premium text-center">
                                    <div class="input-group input-group-sm justify-content-center" style="max-width:120px;margin:auto;">
                                        <input type="number" name="sub_premium_cashback" step="0.1" min="0" max="100"
                                               class="form-control config-input"
                                               value="{{ $data['sub_premium_cashback'] ?? '2.0' }}">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="feature-sub mt-1">por pedido completado</div>
                                </td>
                            </tr>

                            {{-- HISTORIAL --}}
                            <tr>
                                <td>
                                    <div class="feature-label fw-semibold">Historial de pedidos</div>
                                </td>
                                <td class="text-center">
                                    <span class="check">✓</span>
                                    <div class="feature-sub">Básico</div>
                                </td>
                                <td class="col-plus text-center">
                                    <span class="check">✓</span>
                                    <div class="feature-sub">Completo</div>
                                </td>
                                <td class="col-premium text-center">
                                    <span class="check">✓</span>
                                    <div class="feature-sub">Completo + exportar</div>
                                </td>
                            </tr>

                            {{-- CTA --}}
                            <tr>
                                <td class="text-muted small">Botón en la app del cliente</td>
                                <td class="text-center">
                                    <span class="badge badge-secondary px-3 py-2" style="font-size:13px;">Gratis</span>
                                </td>
                                <td class="col-plus text-center">
                                    <span class="badge px-3 py-2" style="background:#1565c0;color:#fff;font-size:13px;">Suscribir</span>
                                </td>
                                <td class="col-premium text-center">
                                    <span class="badge px-3 py-2" style="background:#6a1b9a;color:#fff;font-size:13px;">Suscribir ya</span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 border-0">
                <button type="reset" class="btn btn-outline-secondary">Restablecer</button>
                <button type="submit" class="btn btn--primary px-4">Guardar cambios</button>
            </div>
        </div>
    </form>

    {{-- Suscriptores recientes --}}
    @php
        $recentSubs = \App\Models\CustomerSubscription::with('user')
            ->where('status', 1)
            ->whereIn('type', ['plus', 'premium'])
            ->latest()
            ->take(10)
            ->get();
    @endphp
    @if($recentSubs->count())
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header border-0">
            <h5 class="card-title mb-0">Suscriptores activos recientes</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSubs as $sub)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $sub->user?->f_name }} {{ $sub->user?->l_name }}</div>
                            <small class="text-muted">{{ $sub->user?->phone }}</small>
                        </td>
                        <td>
                            @if($sub->type === 'premium')
                                <span class="badge px-2" style="background:#f3e5f5;color:#6a1b9a;">🟣 Premium</span>
                            @else
                                <span class="badge px-2" style="background:#e3f2fd;color:#1565c0;">🔵 Plus</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->expires_at)
                                <span class="{{ $sub->expires_at->isPast() ? 'text-danger' : 'text-success' }}">
                                    {{ $sub->expires_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">Sin vencimiento</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->isActive())
                                <span class="badge badge-soft-success">Activa</span>
                            @else
                                <span class="badge badge-soft-danger">Vencida</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
