{{-- ============================================================
     PERFIL GAMIFICADO ZARPERO
     Incluir en info.blade.php con:
     @include('admin-views.delivery-man.partials._zarpero_profile', ['profile' => $zarperoProfile])
     ============================================================ --}}
@php
    $stat         = $profile['stat'];
    $level        = $profile['level'];
    $rating       = $profile['rating'];
    $totalOrders  = $profile['totalOrders'];
    $acceptance   = $profile['acceptance'];
    $achievements = $profile['achievements'];
    $bonuses      = $profile['pendingBonuses'];
    $totalBonus   = $profile['totalBonusEarned'];
    $weeklyRank   = $profile['weeklyRank'] !== false ? $profile['weeklyRank'] + 1 : '—';

    $levelColors = [
        'standard' => ['bg' => '#28a745', 'badge' => 'success',  'text' => '🟢'],
        'pro'      => ['bg' => '#007bff', 'badge' => 'primary',  'text' => '🔵'],
        'elite'    => ['bg' => '#6f42c1', 'badge' => 'purple',   'text' => '🟣'],
    ];
    $lc = $levelColors[$level?->slug ?? 'standard'] ?? $levelColors['standard'];

    $xpProgress = $stat->xp_progress;
    $xpLevel    = $stat->level_xp;
@endphp

<div class="card mt-4 border-0 shadow-sm">
    <div class="card-body p-0">

        {{-- Header del nivel --}}
        <div class="rounded-top p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3"
             style="background: linear-gradient(135deg, {{ $lc['bg'] }}, {{ $lc['bg'] }}cc);">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:3rem; line-height:1;">{{ $lc['text'] }}</div>
                <div>
                    <h4 class="mb-0 text-white fw-bold">{{ $level?->name ?? '🟢 Zarpero Base' }}</h4>
                    <small class="opacity-75">{{ $level?->description }}</small>
                </div>
            </div>
            <div class="text-right">
                <div class="h2 mb-0 fw-bold text-white">{{ $level?->driver_percent ?? 88 }}%</div>
                <small class="opacity-75">del envío para ti</small>
            </div>
        </div>

        {{-- XP Bar --}}
        <div class="px-4 py-3 bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-semibold text-dark">⚡ XP Total: {{ number_format($stat->xp) }}</span>
                <span class="text-muted small">Nivel {{ $xpLevel }} → {{ $xpLevel + 1 }}</span>
            </div>
            <div class="progress" style="height:10px; border-radius:10px;">
                <div class="progress-bar bg-warning" style="width:{{ $xpProgress }}%; border-radius:10px;"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">{{ $xpProgress }}/100 XP para siguiente nivel</small>
                <small class="text-muted">🏆 Ranking #{{ $weeklyRank }}</small>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="row g-0 border-bottom text-center">
            <div class="col-6 col-md-3 p-3 border-right">
                <div class="h4 mb-0 fw-bold text-success">{{ $totalOrders }}</div>
                <small class="text-muted">Entregas totales</small>
            </div>
            <div class="col-6 col-md-3 p-3 border-right">
                <div class="h4 mb-0 fw-bold text-warning">
                    {{ $rating > 0 ? number_format($rating, 1) : '—' }} ★
                </div>
                <small class="text-muted">Calificación</small>
            </div>
            <div class="col-6 col-md-3 p-3 border-right">
                <div class="h4 mb-0 fw-bold text-info">{{ $stat->streak_days }}</div>
                <small class="text-muted">🔥 Días de racha</small>
            </div>
            <div class="col-6 col-md-3 p-3">
                <div class="h4 mb-0 fw-bold text-primary">{{ $acceptance }}%</div>
                <small class="text-muted">Aceptación</small>
            </div>
        </div>

        {{-- Entregas este mes + progreso al siguiente nivel --}}
        @if($level)
        <div class="px-4 py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold">📦 Entregas este mes: <strong>{{ $stat->monthly_deliveries }}</strong></span>
                @php
                    $nextLevel = \App\Models\DeliverymanLevel::where('sort_order', '>', $level->sort_order)
                        ->orderBy('sort_order')->first();
                @endphp
                @if($nextLevel)
                    <span class="text-muted small">Próximo nivel: <strong>{{ $nextLevel->name }}</strong></span>
                @else
                    <span class="badge badge-soft-warning">¡Nivel máximo!</span>
                @endif
            </div>
            @if($nextLevel)
            @php
                $needed   = $nextLevel->min_deliveries;
                $current  = $stat->monthly_deliveries;
                $pct      = $needed > 0 ? min(100, round($current / $needed * 100)) : 100;
                $remaining = max(0, $needed - $current);
            @endphp
            <div class="progress mb-1" style="height:8px; border-radius:8px;">
                <div class="progress-bar" style="width:{{ $pct }}%; background:{{ $lc['bg'] }}; border-radius:8px;"></div>
            </div>
            <small class="text-muted">
                {{ $pct }}% — faltan <strong>{{ $remaining }}</strong> entregas para {{ $nextLevel->name }}
                @if($nextLevel->min_rating > $rating)
                    · y calificación <strong>{{ $nextLevel->min_rating }}★</strong>
                @endif
            </small>
            @endif
        </div>
        @endif

        <div class="row g-0">
            {{-- Bonificaciones pendientes --}}
            <div class="col-lg-6 border-right">
                <div class="p-4">
                    <h6 class="fw-bold mb-3">💰 Bonificaciones Pendientes
                        @if($bonuses->count() > 0)
                            <span class="badge badge-soft-success ml-1">{{ $bonuses->count() }}</span>
                        @endif
                    </h6>

                    @if($bonuses->isEmpty())
                        <p class="text-muted small mb-0">Sin bonos pendientes por pagar.</p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($bonuses->take(6) as $bonus)
                        @php
                            $typeIcons = [
                                'volume'     => '📦',
                                'peak_hour'  => '⏰',
                                'rating'     => '⭐',
                                'acceptance' => '🎯',
                                'streak'     => '🔥',
                            ];
                            $icon = $typeIcons[$bonus->type] ?? '💵';
                        @endphp
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <div>
                                <span class="mr-1">{{ $icon }}</span>
                                <span class="small">{{ $bonus->label }}</span>
                                @if($bonus->period)
                                    <span class="badge badge-soft-secondary ml-1 small">{{ $bonus->period }}</span>
                                @endif
                            </div>
                            <span class="fw-bold text-success">L {{ number_format($bonus->amount, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2 pt-2 border-top d-flex justify-content-between">
                        <span class="text-muted small">Total ganado en bonos:</span>
                        <span class="fw-bold text-success">L {{ number_format($totalBonus, 2) }}</span>
                    </div>
                    @endif

                    {{-- Próximos bonos de volumen --}}
                    <div class="mt-3">
                        <small class="text-muted fw-semibold d-block mb-2">🎯 Próximos bonos de volumen:</small>
                        @foreach([20 => 200, 50 => 600, 100 => 1500] as $threshold => $amount)
                        @php $pctV = min(100, round($stat->monthly_deliveries / $threshold * 100)); @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>{{ $threshold }} entregas → <strong>L {{ number_format($amount) }}</strong></small>
                                <small class="text-muted">{{ $stat->monthly_deliveries }}/{{ $threshold }}</small>
                            </div>
                            <div class="progress" style="height:5px; border-radius:5px;">
                                <div class="progress-bar bg-success" style="width:{{ $pctV }}%; border-radius:5px;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Logros / Medallas --}}
            <div class="col-lg-6">
                <div class="p-4">
                    <h6 class="fw-bold mb-3">🏅 Medallas
                        <span class="badge badge-soft-primary ml-1">{{ $achievements->count() }}</span>
                    </h6>

                    @if($achievements->isEmpty())
                        <p class="text-muted small">Aún no ha desbloqueado medallas. ¡Sigue entregando!</p>
                    @else
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($achievements as $ach)
                        @php
                            $achColors = [
                                'gold'   => '#ffc107',
                                'silver' => '#adb5bd',
                                'bronze' => '#cd7f32',
                                'purple' => '#6f42c1',
                            ];
                            $achColor = $achColors[$ach->color] ?? '#adb5bd';
                        @endphp
                        <div class="text-center" style="width:70px;" title="{{ $ach->name }}: {{ $ach->description }}">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                 style="width:48px;height:48px;background:{{ $achColor }}20;border:2px solid {{ $achColor }};">
                                <span style="font-size:1.4rem;">{{ $ach->icon }}</span>
                            </div>
                            <small class="text-muted d-block" style="font-size:10px;line-height:1.2;">{{ Str::limit($ach->name, 15) }}</small>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Racha visual --}}
                    <div class="mt-3 p-3 rounded" style="background:#fff3cd20; border:1px solid #ffc10740;">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size:1.5rem;">🔥</span>
                            <div>
                                <div class="fw-bold">{{ $stat->streak_days }} días de racha</div>
                                <small class="text-muted">
                                    @if($stat->streak_days >= 10) ¡Imparable! Bono de L 350 desbloqueado
                                    @elseif($stat->streak_days >= 5) ¡En llamas! Bono de L 150 desbloqueado
                                    @elseif($stat->streak_days >= 1) {{ 5 - $stat->streak_days }} días más para bono de L 150
                                    @else Trabaja hoy para iniciar tu racha
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
