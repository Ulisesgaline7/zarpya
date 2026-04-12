@extends('layouts.admin.app')
@section('title', 'Conductores de Taxi')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-group-senior" style="font-size:22px"></i></span>
            <span>{{ translate('Conductores de Taxi') }}</span>
        </h1>
        <button class="btn btn-sm btn-primary ml-auto" data-toggle="modal" data-target="#addDriverModal">
            <i class="tio-add"></i> {{ translate('Agregar Conductor') }}
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="GET" class="d-flex gap-2 w-100">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="{{ translate('Nombre o teléfono...') }}" value="{{ $search }}" style="max-width:200px">
                <select name="status" class="form-control form-control-sm" style="max-width:180px" onchange="this.form.submit()">
                    <option value="">{{ translate('Todos los estados') }}</option>
                    <option value="pending"  {{ $status === 'pending'  ? 'selected' : '' }}>{{ translate('Pendientes') }}</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>{{ translate('Aprobados') }}</option>
                    <option value="denied"   {{ $status === 'denied'   ? 'selected' : '' }}>{{ translate('Denegados') }}</option>
                </select>
                <button class="btn btn-sm btn-primary">{{ translate('Buscar') }}</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Conductor') }}</th>
                        <th>{{ translate('Teléfono') }}</th>
                        <th>{{ translate('Zona') }}</th>
                        <th>{{ translate('Vehículo') }}</th>
                        <th>{{ translate('Placa') }}</th>
                        <th>{{ translate('Estado') }}</th>
                        <th>{{ translate('Disponible') }}</th>
                        <th>{{ translate('Registro') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                    @php
                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'denied' => 'danger'];
                        $color = $statusColors[$driver->application_status] ?? 'secondary';
                    @endphp
                    <tr>
                        <td>#{{ $driver->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img class="rounded-circle"
                                     src="{{ $driver->image ? asset('storage/app/public/taxi-driver/'.$driver->image) : asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                     width="32" height="32" alt=""
                                     onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'">
                                <span>{{ $driver->f_name }} {{ $driver->l_name }}</span>
                            </div>
                        </td>
                        <td>{{ $driver->phone }}</td>
                        <td>{{ $driver->zone?->name ?? '—' }}</td>
                        <td><span class="badge badge-soft-info">{{ ucfirst($driver->vehicle_type) }}</span></td>
                        <td>{{ $driver->license_plate ?? '—' }}</td>
                        <td><span class="badge badge-soft-{{ $color }}">{{ translate(ucfirst($driver->application_status)) }}</span></td>
                        <td>
                            @if($driver->available && $driver->active)
                                <span class="badge badge-soft-success">{{ translate('Sí') }}</span>
                            @else
                                <span class="badge badge-soft-secondary">{{ translate('No') }}</span>
                            @endif
                        </td>
                        <td><small>{{ \Carbon\Carbon::parse($driver->created_at)->format('d/m/Y') }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.zarpya.taxi.driver.show', $driver->id) }}"
                                   class="btn btn-xs btn-outline-primary">{{ translate('Ver') }}</a>

                                @if($driver->application_status === 'pending')
                                <form action="{{ route('admin.zarpya.taxi.driver.status') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $driver->id }}">
                                    <input type="hidden" name="status" value="approved">
                                    <button class="btn btn-xs btn-success">{{ translate('Aprobar') }}</button>
                                </form>
                                <form action="{{ route('admin.zarpya.taxi.driver.status') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $driver->id }}">
                                    <input type="hidden" name="status" value="denied">
                                    <button class="btn btn-xs btn-danger">{{ translate('Denegar') }}</button>
                                </form>
                                @endif

                                <form action="{{ route('admin.zarpya.taxi.driver.destroy', $driver->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ translate('¿Eliminar conductor?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="tio-delete"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">{{ translate('Sin conductores registrados') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $drivers->links() }}</div>
    </div>
</div>

{{-- Modal Agregar Conductor --}}
<div class="modal fade" id="addDriverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.zarpya.taxi.driver.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Agregar Conductor de Taxi') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Nombre') }} <span class="text-danger">*</span></label>
                                <input type="text" name="f_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Apellido') }}</label>
                                <input type="text" name="l_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Teléfono') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Email') }}</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Contraseña') }} <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Zona') }} <span class="text-danger">*</span></label>
                                <select name="zone_id" class="form-control" required>
                                    <option value="">{{ translate('Seleccionar...') }}</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Tipo de Vehículo') }} <span class="text-danger">*</span></label>
                                <select name="vehicle_type" class="form-control" required>
                                    <option value="standard">Standard</option>
                                    <option value="premium">Premium</option>
                                    <option value="moto">Moto</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ translate('Placa') }}</label>
                                <input type="text" name="license_plate" class="form-control" maxlength="20">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Guardar') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
