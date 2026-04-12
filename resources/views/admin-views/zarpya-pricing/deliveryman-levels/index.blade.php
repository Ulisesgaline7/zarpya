@extends('layouts.admin.app')
@section('title', 'Niveles Zarpero')

@push('css_or_js')
<style>
.level-card-standard { border-top: 4px solid #28a745; }
.level-card-pro      { border-top: 4px solid #007bff; }
.level-card-elite    { border-top: 4px solid #6f42c1; }
.benefit-item { display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #f0f0f0; font-size:13px; }
.benefit-item:last-child { border-bottom:none; }
.xp-badge { background:linear-gradient(135deg,#ffc107,#ff8c00); color:#fff; border-radius:20px; padding:2px 10px; font-size:11px; font-weight:700; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">🏷️</span>
            <span>Etiquetas de Repartidores — Sistema Zarpero</span>
        </h1>
        <div class="ml-auto d-flex gap-2">
            <a href="{{ route('admin.zarpya.pricing.ranking') }}" class="btn btn-sm btn-warning">
                🏆 Ranking Semanal
            </a>
        </div>
    </div>

    {{-- Stats globales --}}
    <div class="row g-3 mb-4">
        @foreach($levels as $level)
        @php
            $colors = ['standard'=>['bg'=>'success','emoji'=>'🟢'], 'pro'=>['bg'=>'primary','emoji'=>'🔵'], 'elite'=>['bg'=>'purple','emoji'=>'🟣']];
            $c = $colors[$level->slug] ?? $colors['standard'];
        @endphp
        <div class="col-md-4">
            <div class="card text-center py-3 level-card-{{ $level->slug }}">
                <div style="font-size:2rem;">{{ $c['emoji'] }}</div>
                <h5 class="mt-1 mb-0">{{ $level->name }}</h5>
                <div class="h2 fw-bold text-{{ $c['bg'] }} my-1">{{ $level->driver_percent }}%</div>
                <small class="text-muted">del envío para el repartidor</small>
                <div class="mt-2">
                    <span class="badge badge-soft-{{ $c['bg'] }}">{{ $level->deliverymen_count }} Zarperos</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Perfiles detallados --}}
    <div class="row g-3 mb-4">

        {{-- 🟢 Zarpero Base --}}
        <div class="col-lg-4">
            <div class="card h-100 level-card-standard">
                <div class="card-header" style="background:#28a74510;">
                    <h5 class="mb-0">🟢 Zarpero Base</h5>
                    <small class="text-muted">Repartidor nuevo o con poca actividad</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">REQUISITOS</div>
                        <div class="benefit-item"><span>📦</span> Menos de 50 entregas mensuales</div>
                        <div class="benefit-item"><span>⭐</span> Calificación menor a 4.5</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">BENEFICIOS</div>
                        <div class="benefit-item"><span>✅</span> Acceso básico a pedidos</div>
                        <div class="benefit-item"><span>💵</span> Pago estándar por entrega (88%)</div>
                    </div>
                    <div class="p-2 rounded" style="background:#28a74510;">
                        <small class="text-success fw-semibold">💡 Consejo: Completa 50 entregas con 4.6★ para subir a Pro</small>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ $levels->firstWhere('slug','standard')?->deliverymen_count ?? 0 }} repartidores</span>
                    <a href="{{ route('admin.zarpya.pricing.level.edit', $levels->firstWhere('slug','standard')?->id ?? 0) }}"
                       class="btn btn-sm btn-outline-success">Editar</a>
                </div>
            </div>
        </div>

        {{-- 🔵 Zarpero Pro --}}
        <div class="col-lg-4">
            <div class="card h-100 level-card-pro">
                <div class="card-header" style="background:#007bff10;">
                    <h5 class="mb-0">🔵 Zarpero Pro</h5>
                    <small class="text-muted">Buen historial, pocos rechazos</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">REQUISITOS</div>
                        <div class="benefit-item"><span>📦</span> +50 entregas al mes</div>
                        <div class="benefit-item"><span>⭐</span> Calificación mínima 4.6★</div>
                        <div class="benefit-item"><span>✅</span> Buen historial (pocos rechazos)</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">BENEFICIOS</div>
                        <div class="benefit-item"><span>🚀</span> Prioridad media en asignación</div>
                        <div class="benefit-item"><span>💰</span> Bonificación por volumen semanal</div>
                        <div class="benefit-item"><span>📈</span> Acceso a pedidos mejor pagados</div>
                        <div class="benefit-item"><span>💵</span> 91% del envío</div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ $levels->firstWhere('slug','pro')?->deliverymen_count ?? 0 }} repartidores</span>
                    <a href="{{ route('admin.zarpya.pricing.level.edit', $levels->firstWhere('slug','pro')?->id ?? 0) }}"
                       class="btn btn-sm btn-outline-primary">Editar</a>
                </div>
            </div>
        </div>

        {{-- 🟣 Zarpero Elite --}}
        <div class="col-lg-4">
            <div class="card h-100 level-card-elite">
                <div class="card-header" style="background:#6f42c110;">
                    <h5 class="mb-0">🟣 Zarpero Elite</h5>
                    <small class="text-muted">Alta puntualidad y aceptación</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">REQUISITOS</div>
                        <div class="benefit-item"><span>📦</span> +150 entregas al mes</div>
                        <div class="benefit-item"><span>⭐</span> Calificación 4.8+</div>
                        <div class="benefit-item"><span>🎯</span> Alta puntualidad y aceptación</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold text-muted small mb-2">BENEFICIOS</div>
                        <div class="benefit-item"><span>🚀</span> Máxima prioridad en pedidos</div>
                        <div class="benefit-item"><span>💎</span> Bonos rendimiento + horas pico</div>
                        <div class="benefit-item"><span>👑</span> Acceso a pedidos VIP (mayor ticket)</div>
                        <div class="benefit-item"><span>🛡️</span> Soporte prioritario</div>
                        <div class="benefit-item"><span>💵</span> 93% del envío</div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">{{ $levels->firstWhere('slug','elite')?->deliverymen_count ?? 0 }} repartidores</span>
                    <a href="{{ route('admin.zarpya.pricing.level.edit', $levels->firstWhere('slug','elite')?->id ?? 0) }}"
                       class="btn btn-sm btn-outline-primary" style="border-color:#6f42c1;color:#6f42c1;">Editar</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sistema de Bonificaciones --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">💰 Sistema de Bonificaciones</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <h6 class="fw-bold">🔥 Bonos por Volumen</h6>
                            <div class="benefit-item"><span>📦</span> 20 entregas = <strong>L 200</strong></div>
                            <div class="benefit-item"><span>📦</span> 50 entregas = <strong>L 600</strong></div>
                            <div class="benefit-item"><span>📦</span> 100 entregas = <strong>L 1,500</strong></div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="fw-bold">⏰ Bonos Horas Pico</h6>
                            <div class="benefit-item"><span>🍽️</span> Almuerzo 11am–2pm: <strong>+15%</strong></div>
                            <div class="benefit-item"><span>🌙</span> Cena 6pm–9pm: <strong>+20%</strong></div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="fw-bold">⭐ Bonos por Calificación</h6>
                            <div class="benefit-item"><span>⭐</span> 4.8+ = <strong>L 150/semana</strong></div>
                            <div class="benefit-item"><span>🌟</span> 4.9+ = <strong>L 300/semana</strong></div>
                            <div class="benefit-item"><span>🎯</span> +90% aceptación = <strong>L 100/semana</strong></div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="fw-bold">🚀 Bonos de Racha</h6>
                            <div class="benefit-item"><span>🔥</span> 5 días seguidos = <strong>L 150</strong></div>
                            <div class="benefit-item"><span>💎</span> 10 días seguidos = <strong>L 350</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sistema XP / Gamificación --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">⚡ Sistema XP</h5></div>
                <div class="card-body">
                    <div class="benefit-item"><span>📦</span> Cada entrega = <strong>10 XP base</strong></div>
                    <div class="benefit-item"><span>💵</span> +1 XP por cada L50 del pedido</div>
                    <div class="benefit-item"><span>🏅</span> Logros = XP extra (50–1000 XP)</div>
                    <div class="benefit-item"><span>📊</span> Cada 100 XP = subir 1 nivel de experiencia</div>
                    <div class="mt-3 p-3 rounded" style="background:#ffc10720;border:1px solid #ffc10740;">
                        <small class="fw-semibold">🎮 El XP es visible en el perfil del repartidor y en el ranking semanal</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">🏅 Logros Disponibles</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr><th>Medalla</th><th>Condición</th><th>XP</th></tr>
                            </thead>
                            <tbody>
                                @foreach($achievements as $ach)
                                <tr>
                                    <td>{{ $ach->icon }} {{ $ach->name }}</td>
                                    <td><small class="text-muted">{{ $ach->description }}</small></td>
                                    <td><span class="xp-badge">+{{ $ach->xp_reward }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Crear nuevo nivel --}}
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">➕ Crear nuevo nivel</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.pricing.level.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Slug</label>
                                    <input type="text" name="slug" class="form-control" placeholder="premium" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Nombre</label>
                                    <input type="text" name="name" class="form-control" placeholder="🟤 Zarpero Premium" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">% para repartidor</label>
                                    <input type="number" name="driver_percent" class="form-control" value="90" step="0.1" min="50" max="100" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Min. entregas/mes</label>
                                    <input type="number" name="min_deliveries" class="form-control" value="0" min="0" required>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="input-label">Rating mínimo</label>
                                    <input type="number" name="min_rating" class="form-control" value="0" step="0.1" min="0" max="5" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Meses activo mínimo</label>
                            <input type="number" name="min_months_active" class="form-control" value="0" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Crear nivel</button>
                    </form>
                </div>
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
