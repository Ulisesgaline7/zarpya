@extends('layouts.landing.app')
@section('title', translate('Registro de Zarperos - Zarpya'))

@section('content')
<style>
    .dm-registration-section {
        background: #f8fafc;
        padding: 100px 0;
    }
    .registration-card {
        border-radius: 40px;
        border: none;
        box-shadow: 0 25px 50px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .registration-header {
        background: var(--black-pure);
        color: white;
        padding: 60px;
        text-align: center;
    }
    .form-label {
        font-weight: 600;
        color: var(--deep-blue);
        margin-bottom: 10px;
    }
    .form-control {
        border-radius: 18px;
        padding: 15px 22px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .form-control:focus {
        border-color: var(--cyan-tech);
        box-shadow: 0 0 0 4px rgba(47, 185, 203, 0.1);
    }
    .card-title-maritime {
        display: flex;
        align-items: center;
        gap: 15px;
        color: var(--deep-blue);
        font-weight: 700;
        margin-bottom: 30px;
        font-family: var(--font-display);
    }
    .card-title-maritime i {
        color: var(--cyan-tech);
        font-size: 1.5rem;
    }
    .upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 25px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-box:hover {
        border-color: var(--cyan-tech);
        background: rgba(47, 185, 203, 0.05);
    }
</style>

<section class="dm-registration-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="registration-card bg-white">
                    <div class="registration-header">
                        <div class="contact-icon mx-auto mb-4" style="background: var(--cyan-tech); width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem;">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <h1 class="display-font" style="font-size: 3rem;">Únete como <span style="color: var(--cyan-tech);">Zarpero</span></h1>
                        <p class="fs-5 opacity-75 mt-3">Sé el capitán de tu propio tiempo y genera ingresos con la flota de Zarpya.</p>
                    </div>

                    <div class="p-5">
                        <form action="{{ route('deliveryman.store') }}" method="post" enctype="multipart/form-data" id="form-id">
                            @csrf
                            
                            <!-- Datos Personales -->
                            <div class="mb-5">
                                <h4 class="card-title-maritime"><i class="fas fa-user-tie"></i> Información del Capitán</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre(s)</label>
                                        <input type="text" name="f_name" class="form-control" placeholder="Ej: Juan Ramón" required value="{{ old('f_name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Apellido(s)</label>
                                        <input type="text" name="l_name" class="form-control" placeholder="Ej: Pérez García" required value="{{ old('l_name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico</label>
                                        <input type="email" name="email" class="form-control" placeholder="capitan@ejemplo.com" required value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de Zarpero</label>
                                        <select name="earning" class="form-control">
                                            <option value="1">Freelance (Zarpa cuando quieras)</option>
                                            <option value="0">Base (Sueldo Fijo)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Documentación y Zona -->
                            <div class="mb-5">
                                <h4 class="card-title-maritime"><i class="fas fa-compass"></i> Ruta y Documentación</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Zona de Navegación</label>
                                        <select name="zone_id" class="form-control" required>
                                            <option value="" hidden>Selecciona tu ciudad/zona</option>
                                            @foreach (\App\Models\Zone::active()->get() as $zone)
                                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de Vehículo</label>
                                        <select name="vehicle_id" class="form-control" required>
                                            <option value="" hidden>¿En qué vas a zarpar?</option>
                                            @foreach (\App\Models\DMVehicle::where('status',1)->get(['id','type']) as $v)
                                                <option value="{{ $v->id }}">{{ $v->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de Identificación</label>
                                        <select name="identity_type" class="form-control">
                                            <option value="nid">DNI (Identidad Honduras)</option>
                                            <option value="driving_license">Licencia de Conducir</option>
                                            <option value="passport">Pasaporte</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Número de Identificación</label>
                                        <input type="text" name="identity_number" class="form-control" placeholder="0000-0000-00000" required value="{{ old('identity_number') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Acceso a la Bitácora -->
                            <div class="mb-5">
                                <h4 class="card-title-maritime"><i class="fas fa-key"></i> Acceso a la Bitácora</h4>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Número de Teléfono</label>
                                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="+504 0000-0000" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contraseña Segura</label>
                                        <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Foto del Zarpero</label>
                                    <div class="upload-box" onclick="document.getElementById('customFileEg1').click();">
                                        <img id="viewer" src="{{ asset('public/assets/admin/img/upload-img.png') }}" style="max-height: 120px;" alt="">
                                        <p class="mt-2 small text-muted">Haz clic para subir tu foto de perfil (1:1)</p>
                                        <input type="file" name="image" id="customFileEg1" hidden accept="image/*" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fotos de Identificación</label>
                                    <div id="coba" class="row g-2"></div>
                                    <p class="small text-muted mt-2">Sube fotos claras de tu DNI o Licencia.</p>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="cmn--btn px-5 py-3 fs-5 border-0 shadow-lg" style="min-width: 300px;">
                                    ¡Zarpar Ahora!
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script_2')
<script>
    // Preview de imagen
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#customFileEg1").change(function () {
        readURL(this);
    });
</script>
@endpush
