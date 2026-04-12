@extends('layouts.admin.app')
@section('title', 'Editar Vehículo')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-car-outlined" style="font-size:22px"></i></span>
            <span>Editar Vehículo</span>
        </h1>
        <a href="{{ route('admin.zarpya.rental.vehicles') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-arrow-backward"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">{{ $vehicle->brand }} {{ $vehicle->model }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.rental.vehicle.update', $vehicle->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Marca</label>
                                    <input type="text" name="brand" class="form-control" value="{{ $vehicle->brand }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Modelo</label>
                                    <input type="text" name="model" class="form-control" value="{{ $vehicle->model }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Placa</label>
                                    <input type="text" name="plate" class="form-control" value="{{ $vehicle->plate }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Color</label>
                                    <input type="text" name="color" class="form-control" value="{{ $vehicle->color }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio/Hora (L)</label>
                                    <input type="number" name="price_per_hour" class="form-control" value="{{ $vehicle->price_per_hour }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Precio/Día (L)</label>
                                    <input type="number" name="price_per_day" class="form-control" value="{{ $vehicle->price_per_day }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Depósito (L)</label>
                                    <input type="number" name="deposit" class="form-control" value="{{ $vehicle->deposit }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Asientos</label>
                                    <input type="number" name="seats" class="form-control" value="{{ $vehicle->seats }}" min="1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">% Propietario</label>
                                    <input type="number" name="owner_percent" class="form-control" value="{{ $vehicle->owner_percent }}" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">% Zarpya</label>
                                    <input type="number" name="platform_percent" class="form-control" value="{{ $vehicle->platform_percent }}" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Estado</label>
                            <select name="status" class="form-control" required>
                                <option value="available" {{ $vehicle->status === 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="rented" {{ $vehicle->status === 'rented' ? 'selected' : '' }}>Rentado</option>
                                <option value="maintenance" {{ $vehicle->status === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="inactive" {{ $vehicle->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" name="with_driver" value="1" class="custom-control-input" id="with_driver" {{ $vehicle->with_driver ? 'checked' : '' }}>
                            <label class="custom-control-label" for="with_driver">Incluye conductor</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection