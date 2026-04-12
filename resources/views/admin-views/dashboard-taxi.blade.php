@extends('layouts.admin.app')

@section('title', \App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value ?? translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    @php
        $mod = \App\Models\Module::find(\Illuminate\Support\Facades\Config::get("module.current_module_id"));

        // Conductores de Taxi (tabla propia, separada de delivery_men)
        $total_drivers    = DB::table('taxi_drivers')->count();
        $active_drivers   = DB::table('taxi_drivers')->where('active', 1)->where('application_status', 'approved')->count();
        $inactive_drivers = DB::table('taxi_drivers')->where('active', 0)->where('application_status', 'approved')->count();
        $pending_drivers  = DB::table('taxi_drivers')->where('application_status', 'pending')->count();
        $new_drivers      = DB::table('taxi_drivers')->where('created_at', '>=', now()->subDays(30))->count();

        // Viajes de Taxi (tabla propia taxi_rides)
        $total_trips     = DB::table('taxi_rides')->count();
        $completed_trips = DB::table('taxi_rides')->where('status', 'completed')->count();
        $pending_trips   = DB::table('taxi_rides')->where('status', 'searching')->count();
        $canceled_trips  = DB::table('taxi_rides')->where('status', 'cancelled')->count();
        $ongoing_trips   = DB::table('taxi_rides')->whereIn('status', ['accepted', 'arriving', 'in_progress'])->count();
        $today_trips     = DB::table('taxi_rides')->whereDate('created_at', now())->count();

        $total_customers  = DB::table('users')->count();
        $active_customers = DB::table('users')->where('status', 1)->count();
        $new_customers    = DB::table('users')->where('created_at', '>=', now()->subDays(30))->count();

        $total_earnings = DB::table('taxi_rides')->where('status', 'completed')->sum('platform_earning');
        $today_earnings = DB::table('taxi_rides')->where('status', 'completed')->whereDate('created_at', now())->sum('platform_earning');

        $trips_by_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $trips_by_month[$i] = DB::table('taxi_rides')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->count();
        }

        $top_drivers = DB::table('taxi_drivers')
            ->where('taxi_drivers.application_status', 'approved')
            ->leftJoin('taxi_rides', function($join) {
                $join->on('taxi_rides.driver_id', '=', 'taxi_drivers.id')
                     ->where('taxi_rides.status', 'completed');
            })
            ->select(
                'taxi_drivers.id', 'taxi_drivers.f_name', 'taxi_drivers.l_name', 'taxi_drivers.image',
                DB::raw('COUNT(taxi_rides.id) as total_trips'),
                DB::raw('SUM(taxi_rides.driver_earning) as total_earned')
            )
            ->groupBy('taxi_drivers.id', 'taxi_drivers.f_name', 'taxi_drivers.l_name', 'taxi_drivers.image')
            ->orderByDesc('total_trips')
            ->limit(5)
            ->get();

        $recent_trips = DB::table('taxi_rides')
            ->leftJoin('users', 'taxi_rides.customer_id', '=', 'users.id')
            ->leftJoin('taxi_drivers', 'taxi_rides.driver_id', '=', 'taxi_drivers.id')
            ->select(
                'taxi_rides.id', 'taxi_rides.status', 'taxi_rides.total_fare', 'taxi_rides.created_at',
                'users.f_name as customer_fname', 'users.l_name as customer_lname',
                DB::raw("CONCAT(COALESCE(taxi_drivers.f_name,''), ' ', COALESCE(taxi_drivers.l_name,'')) as driver_name")
            )
            ->orderByDesc('taxi_rides.created_at')
            ->limit(8)
            ->get();

        $base_price       = $mod->base_price ?? 0;
        $price_per_km     = $mod->price_per_km ?? 0;
        $price_per_minute = $mod->price_per_minute ?? 0;
        $minimum_fare     = $mod->minimum_fare ?? 0;
        $commission_pct   = $mod->commission_percent ?? 0;
    @endphp

    <div class="page-header">
        <div class="row align-items-center py-2">
            <div class="col-sm mb-2 mb-sm-0">
                <div class="d-flex align-items-center gap-2">
                    @if($mod && $mod->icon_full_url)
                        <img class="onerror-image" src="{{ $mod->icon_full_url }}" width="38" alt="img">
                    @else
                        <i class="tio-car" style="font-size:38px; color:#005555;"></i>
                    @endif
                    <div class="w-0 flex-grow pl-2">
                        <h1 class="page-header-title mb-0">{{ translate($mod->module_name ?? 'Taxi') }} — {{ translate('Dashboard') }}</h1>
                        <p class="page-header-text m-0">{{ translate('Gestiona conductores, viajes y tarifas del módulo taxi.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-soft-primary rounded-circle flex-shrink-0">
                        <i class="tio-user-outlined fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_drivers }}</h3>
                        <small class="text-muted">{{ translate('Conductores de Taxi') }}</small>
                        <div><span class="badge badge-soft-success">{{ $active_drivers }} {{ translate('activos') }}</span></div>
                    </div>
                </div>
                <div class="card-footer pt-0 border-0">
                    <small class="text-muted">+{{ $new_drivers }} {{ translate('este mes') }}</small>
                    &nbsp;·&nbsp;
                    <small class="text-warning">{{ $pending_drivers }} {{ translate('pendientes') }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-soft-info rounded-circle flex-shrink-0">
                        <i class="tio-car fs-3 text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_trips }}</h3>
                        <small class="text-muted">{{ translate('Viajes Totales') }}</small>
                        <div><span class="badge badge-soft-info">{{ $today_trips }} {{ translate('hoy') }}</span></div>
                    </div>
                </div>
                <div class="card-footer pt-0 border-0">
                    <small class="text-success">{{ $completed_trips }} {{ translate('completados') }}</small>
                    &nbsp;·&nbsp;
                    <small class="text-danger">{{ $canceled_trips }} {{ translate('cancelados') }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-soft-warning rounded-circle flex-shrink-0">
                        <i class="tio-dollar fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ \App\CentralLogics\Helpers::format_currency($total_earnings) }}</h3>
                        <small class="text-muted">{{ translate('Ganancias Plataforma') }}</small>
                        <div><span class="badge badge-soft-warning">{{ \App\CentralLogics\Helpers::format_currency($today_earnings) }} {{ translate('hoy') }}</span></div>
                    </div>
                </div>
                <div class="card-footer pt-0 border-0">
                    <small class="text-muted">{{ $commission_pct }}% {{ translate('comisión plataforma') }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-soft-success rounded-circle flex-shrink-0">
                        <i class="tio-group fs-3 text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $total_customers }}</h3>
                        <small class="text-muted">{{ translate('Clientes') }}</small>
                        <div><span class="badge badge-soft-success">{{ $active_customers }} {{ translate('activos') }}</span></div>
                    </div>
                </div>
                <div class="card-footer pt-0 border-0">
                    <small class="text-muted">+{{ $new_customers }} {{ translate('este mes') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Estado de Viajes --}}
    <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0">{{ translate('Estado de Viajes') }}</h5></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <span class="d-block h4 text-warning mb-0">{{ $pending_trips }}</span>
                    <small class="text-muted">{{ translate('Buscando conductor') }}</small>
                </div>
                <div class="col">
                    <span class="d-block h4 text-info mb-0">{{ $ongoing_trips }}</span>
                    <small class="text-muted">{{ translate('En curso') }}</small>
                </div>
                <div class="col">
                    <span class="d-block h4 text-success mb-0">{{ $completed_trips }}</span>
                    <small class="text-muted">{{ translate('Completados') }}</small>
                </div>
                <div class="col">
                    <span class="d-block h4 text-danger mb-0">{{ $canceled_trips }}</span>
                    <small class="text-muted">{{ translate('Cancelados') }}</small>
                </div>
                <div class="col">
                    <span class="d-block h4 text-secondary mb-0">{{ $pending_drivers }}</span>
                    <small class="text-muted">{{ translate('Conductores pendientes') }}</small>
                </div>
                <div class="col">
                    <span class="d-block h4 text-secondary mb-0">{{ $inactive_drivers }}</span>
                    <small class="text-muted">{{ translate('Conductores inactivos') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">{{ translate('Viajes por Mes') }} ({{ date('Y') }})</h5></div>
                <div class="card-body"><canvas id="tripsChart" height="120"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">{{ translate('Tarifas del Módulo') }}</h5></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ translate('Tarifa base') }}</span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($base_price) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ translate('Por km') }}</span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($price_per_km) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ translate('Por minuto') }}</span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($price_per_minute) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ translate('Tarifa mínima') }}</span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($minimum_fare) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ translate('Comisión') }}</span>
                            <strong>{{ $commission_pct }}%</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ translate('Top Conductores') }}</h5>
                    <a href="{{ route('admin.zarpya.taxi.drivers') }}" class="btn btn-sm btn-outline-primary">{{ translate('Ver todos') }}</a>
                </div>
                <div class="card-body p-0">
                    @forelse($top_drivers as $driver)
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <img class="rounded-circle"
                             src="{{ $driver->image ? asset('storage/app/public/taxi-driver/'.$driver->image) : asset('public/assets/admin/img/160x160/img1.jpg') }}"
                             width="40" height="40" alt="driver"
                             onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $driver->f_name }} {{ $driver->l_name }}</div>
                            <small class="text-muted">{{ $driver->total_trips }} {{ translate('viajes') }}</small>
                        </div>
                        <small class="text-success fw-semibold">{{ \App\CentralLogics\Helpers::format_currency($driver->total_earned ?? 0) }}</small>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">{{ translate('Sin conductores aún') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ translate('Viajes Recientes') }}</h5>
                    <a href="{{ route('admin.zarpya.taxi.rides') }}" class="btn btn-sm btn-outline-primary">{{ translate('Ver todos') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Cliente') }}</th>
                                    <th>{{ translate('Conductor') }}</th>
                                    <th>{{ translate('Monto') }}</th>
                                    <th>{{ translate('Estado') }}</th>
                                    <th>{{ translate('Fecha') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_trips as $trip)
                                @php
                                    $statusColors = [
                                        'searching'   => 'warning',
                                        'accepted'    => 'info',
                                        'arriving'    => 'info',
                                        'in_progress' => 'primary',
                                        'completed'   => 'success',
                                        'cancelled'   => 'danger',
                                    ];
                                    $color = $statusColors[$trip->status] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td><small>#{{ $trip->id }}</small></td>
                                    <td><small>{{ trim($trip->customer_fname . ' ' . $trip->customer_lname) ?: '—' }}</small></td>
                                    <td><small>{{ trim($trip->driver_name) ?: '—' }}</small></td>
                                    <td><small class="fw-semibold">{{ \App\CentralLogics\Helpers::format_currency($trip->total_fare) }}</small></td>
                                    <td><span class="badge badge-soft-{{ $color }}">{{ translate(ucfirst(str_replace('_', ' ', $trip->status))) }}</span></td>
                                    <td><small class="text-muted">{{ \Carbon\Carbon::parse($trip->created_at)->format('d M, H:i') }}</small></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">{{ translate('Sin viajes aún') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
new Chart(document.getElementById('tripsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        datasets: [{
            label: '{{ translate("Viajes") }}',
            data: [{{ implode(',', array_values($trips_by_month)) }}],
            backgroundColor: 'rgba(0,85,85,0.7)',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
