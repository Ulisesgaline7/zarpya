@extends('layouts.admin.app')
@section('title', 'Conductor de Taxi')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <a href="{{ route('admin.zarpya.taxi.drivers') }}" class="btn btn-sm btn-outline-secondary mr-2">
                <i class="tio-arrow-backward"></i>
            </a>
            {{ translate('Conductor') }}: {{ $driver->f_name }} {{ $driver->l_name }}
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img class="rounded-circle mb-3"
                         src="{{ $driver->image ? asset('storage/app/public/taxi-driver/'.$driver->image) : asset('public/assets/admin/img/160x160/img1.jpg') }}"
                         width="80" height="80" alt=""
                         onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                    <h5 class="mb-0">{{ $driver->f_name }} {{ $driver->l_name }}</h5>
                    <small class="text-muted">{{ $driver->phone }}</small>
                    <div class="mt-2">
                        @php $statusColors = ['pending'=>'warning','approved'=>'success','denied'=>'danger'] @endphp
                        <span class="badge badge-soft-{{ $statusColors[$driver->application_status] ?? 'secondary' }}">
                            {{ translate(ucfirst($driver->application_status)) }}
                        </span>
                        @if($driver->available && $driver->active)
                            <span class="badge badge-soft-success ml-1">{{ translate('Disponible') }}</span>
                        @endif
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Email') }}</span>
                        <span>{{ $driver->email ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Zona') }}</span>
                        <span>{{ $driver->zone?->name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Vehículo') }}</span>
                        <span class="badge badge-soft-info">{{ ucfirst($driver->vehicle_type) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Placa') }}</span>
                        <span>{{ $driver->license_plate ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Licencia') }}</span>
                        <span>{{ $driver->license_number ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Ganancias') }}</span>
                        <span class="text-success fw-semibold">{{ \App\CentralLogics\Helpers::format_currency($driver->earning) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">{{ translate('Registro') }}</span>
                        <span>{{ $driver->created_at->format('d/m/Y') }}</span>
                    </li>
                </ul>
                @if($driver->application_status === 'pending')
                <div class="card-footer d-flex gap-2">
                    <form action="{{ route('admin.zarpya.taxi.driver.status') }}" method="POST" class="flex-fill">
                        @csrf
                        <input type="hidden" name="id" value="{{ $driver->id }}">
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success btn-block">{{ translate('Aprobar') }}</button>
                    </form>
                    <form action="{{ route('admin.zarpya.taxi.driver.status') }}" method="POST" class="flex-fill">
                        @csrf
                        <input type="hidden" name="id" value="{{ $driver->id }}">
                        <input type="hidden" name="status" value="denied">
                        <button class="btn btn-danger btn-block">{{ translate('Denegar') }}</button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ translate('Historial de Viajes') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Origen') }}</th>
                                <th>{{ translate('Destino') }}</th>
                                <th>{{ translate('Km') }}</th>
                                <th>{{ translate('Total') }}</th>
                                <th>{{ translate('Ganancia') }}</th>
                                <th>{{ translate('Estado') }}</th>
                                <th>{{ translate('Fecha') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($driver->rides()->latest()->take(20)->get() as $ride)
                            @php
                                $sc = ['searching'=>'secondary','accepted'=>'info','arriving'=>'info','in_progress'=>'warning','completed'=>'success','cancelled'=>'danger'];
                                $c = $sc[$ride->status] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>#{{ $ride->id }}</td>
                                <td><small class="text-truncate d-block" style="max-width:120px" title="{{ $ride->pickup_address }}">{{ Str::limit($ride->pickup_address, 20) }}</small></td>
                                <td><small class="text-truncate d-block" style="max-width:120px" title="{{ $ride->dropoff_address }}">{{ Str::limit($ride->dropoff_address, 20) }}</small></td>
                                <td>{{ $ride->distance_km }}</td>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($ride->total_fare) }}</td>
                                <td class="text-success">{{ \App\CentralLogics\Helpers::format_currency($ride->driver_earning) }}</td>
                                <td><span class="badge badge-soft-{{ $c }}">{{ ucfirst(str_replace('_',' ',$ride->status)) }}</span></td>
                                <td><small>{{ $ride->created_at->format('d/m H:i') }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">{{ translate('Sin viajes aún') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
