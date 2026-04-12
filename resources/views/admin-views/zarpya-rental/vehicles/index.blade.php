@extends('layouts.admin.app')
@section('title', 'Flota de Renta')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-car-outlined" style="font-size:22px"></i></span>
            <span>Módulo Renta de Vehículos</span>
        </h1>
        <a href="{{ route('admin.zarpya.rental.bookings') }}" class="btn btn-sm btn-outline-primary ml-auto">
            <i class="tio-list"></i> Ver reservas
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-success">{{ $stats['available'] }}</div>
                <small class="text-muted">Disponibles</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-primary">{{ $stats['rented'] }}</div>
                <small class="text-muted">Rentados</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center py-3">
                <div class="h3 mb-0 text-warning">{{ $stats['maintenance'] }}</div>
                <small class="text-muted">En mantenimiento</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- FORM --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Agregar vehículo</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.rental.vehicle.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Tipo</label>
                                    <select name="type" class="form-control" required>
                                        <option value="car">Auto</option>
                                        <option value="pickup">Pick-up</option>
                                        <option value="van">Van</option>
                                        <option value="moto">Moto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Zona</label>
                                    <select name="zone_id" class="form-control">
                                        <option value="">Sin zona</option>
                                        @foreach($zones as $z)
                                            <option value="{{ $z->id }}">{{ $z->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Marca</label>
                                    <input type="text" name="brand" class="form-control" placeholder="Toyota">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Modelo</label>
                                    <input type="text" name="model" class="form-control" placeholder="Hilux">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Placa</label>
                                    <input type="text" name="plate" class="form-control" placeholder="KLM-1234">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Color</label>
                                    <input type="text" name="color" class="form-control" placeholder="Blanco">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio/Hora (L)</label>
                                    <input type="number" name="price_per_hour" class="form-control" value="0" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio/Día (L)</label>
                                    <input type="number" name="price_per_day" class="form-control" value="0" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Depósito (L)</label>
                                    <input type="number" name="deposit" class="form-control" value="0" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Asientos</label>
                                    <input type="number" name="seats" class="form-control" value="4" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">% Propietario</label>
                                    <input type="number" name="owner_percent" class="form-control" value="80" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">% Zarpya</label>
                                    <input type="number" name="platform_percent" class="form-control" value="20" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" name="with_driver" value="1" class="custom-control-input" id="with_driver">
                            <label class="custom-control-label" for="with_driver">Incluye conductor</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Agregar a flota</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLA --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Flota</h5>
                    <form method="GET" class="ml-auto" style="min-width:220px">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Marca, modelo, placa..." value="{{ $search }}">
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Vehículo</th>
                                <th>Zona</th>
                                <th>Precio/Hr</th>
                                <th>Precio/Día</th>
                                <th>Depósito</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $v)
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $v->brand }} {{ $v->model }}</span>
                                    <br><small class="text-muted">{{ $v->plate }} · {{ $v->color }} · {{ $v->seats }} asientos</small>
                                </td>
                                <td>{{ $v->zone?->name ?? '—' }}</td>
                                <td>L{{ number_format($v->price_per_hour, 2) }}</td>
                                <td>L{{ number_format($v->price_per_day, 2) }}</td>
                                <td>L{{ number_format($v->deposit, 2) }}</td>
                                <td>
                                    @php $statusColors = ['available'=>'success','rented'=>'primary','maintenance'=>'warning','inactive'=>'secondary'] @endphp
                                    <span class="badge badge-{{ $statusColors[$v->status] ?? 'secondary' }}">{{ ucfirst($v->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.zarpya.rental.vehicle.edit', $v->id) }}"
                                        class="btn btn-sm btn-outline-primary"><i class="tio-edit"></i></a>
                                    <form action="{{ route('admin.zarpya.rental.vehicle.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="tio-delete"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $vehicles->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
