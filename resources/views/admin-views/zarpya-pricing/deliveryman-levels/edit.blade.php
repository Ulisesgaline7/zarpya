@extends('layouts.admin.app')
@section('title', 'Editar Nivel de Repartidor')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon"><i class="tio-user-outlined" style="font-size:22px"></i></span>
            <span>Editar Nivel</span>
        </h1>
        <a href="{{ route('admin.zarpya.pricing.levels') }}" class="btn btn-sm btn-outline-secondary ml-auto">
            <i class="tio-arrow-backward"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Editar nivel: {{ $level->name }}</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.zarpya.pricing.level.update', $level->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="input-label">Nombre</label>
                            <input type="text" name="name" class="form-control" value="{{ $level->name }}" required>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="2">{{ $level->description }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">% para repartidor</label>
                                    <input type="number" name="driver_percent" class="form-control" value="{{ $level->driver_percent }}" step="0.1" min="50" max="100" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Min. entregas</label>
                                    <input type="number" name="min_deliveries" class="form-control" value="{{ $level->min_deliveries }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Rating mínimo</label>
                                    <input type="number" name="min_rating" class="form-control" value="{{ $level->min_rating }}" step="0.1" min="0" max="5" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label">Meses activo mínimo</label>
                                    <input type="number" name="min_months_active" class="form-control" value="{{ $level->min_months_active }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection