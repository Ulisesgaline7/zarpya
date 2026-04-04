@extends('layouts.landing.app')

@section('title', translate('Contacto - Zarpya'))

@section('content')
<style>
    .contact-header {
        background: linear-gradient(rgba(0,1,0,0.85), rgba(21,38,62,0.95)), url('https://images.unsplash.com/photo-1494452672938-ad7f60625bb4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 150px 0 100px;
        text-align: center;
    }
    .contact-section {
        padding: 120px 0;
        background: white;
    }
    .contact-card {
        border-radius: 40px;
        padding: 45px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.3s ease;
    }
    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }
    .contact-icon {
        width: 70px;
        height: 70px;
        background: var(--cyan-tech);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        margin-bottom: 30px;
    }
    .form-control {
        border-radius: 20px;
        padding: 18px 25px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: var(--deep-blue);
    }
    .form-control:focus {
        border-color: var(--cyan-tech);
        box-shadow: 0 0 0 4px rgba(47, 185, 203, 0.1);
    }
    .submit-btn {
        background: var(--cyan-tech);
        color: white;
        border-radius: 20px;
        padding: 18px 45px;
        font-weight: 700;
        border: none;
        transition: all 0.3s ease;
    }
    .submit-btn:hover {
        background: var(--deep-blue);
        transform: scale(1.02);
    }
</style>

<section class="contact-header">
    <div class="container">
        <span class="maritime-badge" style="background: var(--digital-purple);">S.O.S</span>
        <h1 class="display-font wow fadeInUp" style="font-size: 4rem;">Comunícate con Zarpya</h1>
        <p class="fs-4 opacity-75 mt-3 wow fadeInUp" data-wow-delay="0.1s">Nuestra tripulación está lista para ayudarte.</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="contact-card wow fadeInUp">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <h4 class="display-font mb-2">Llamada a Bordo</h4>
                            <p class="text-muted mb-3">¿Necesitas soporte inmediato? Llámanos:</p>
                            <a href="tel:{{ \App\CentralLogics\Helpers::get_settings('phone') }}" class="text-decoration-none fs-4 fw-bold" style="color: var(--deep-blue);">
                                {{ \App\CentralLogics\Helpers::get_settings('phone') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="contact-card wow fadeInUp" data-wow-delay="0.1s">
                            <div class="contact-icon" style="background: var(--digital-purple);"><i class="fas fa-envelope"></i></div>
                            <h4 class="display-font mb-2">Envíanos un Correo</h4>
                            <p class="text-muted mb-3">Escríbenos para consultas o propuestas:</p>
                            <a href="mailto:{{ \App\CentralLogics\Helpers::get_settings('email_address') }}" class="text-decoration-none fs-5 fw-bold" style="color: var(--deep-blue);">
                                {{ \App\CentralLogics\Helpers::get_settings('email_address') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="contact-card wow fadeInUp" data-wow-delay="0.2s">
                            <div class="contact-icon" style="background: var(--black-pure);"><i class="fas fa-map-marker-alt"></i></div>
                            <h4 class="display-font mb-2">Nuestro Puerto Base</h4>
                            <p class="text-muted mb-3">Visítanos en nuestras oficinas centrales:</p>
                            <p class="fs-5 fw-bold mb-0" style="color: var(--deep-blue);">{{ \App\CentralLogics\Helpers::get_settings('address') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="contact-card wow fadeInUp" style="background: white; border: 1px solid #f1f5f9; box-shadow: 0 30px 60px rgba(0,0,0,0.05);">
                    <h2 class="display-font mb-5" style="font-size: 2.5rem;">Lanza un Mensaje</h2>
                    <form action="{{route('send-message')}}" method="post" id="form-id">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-2">Nombre del Capitán</label>
                                <input type="text" name="name" required class="form-control" placeholder="Juan Pérez">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-2">Correo Electrónico</label>
                                <input type="email" name="email" required class="form-control" placeholder="juan@ejemplo.com">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted mb-2">Asunto del Mensaje</label>
                                <input type="text" name="subject" required class="form-control" placeholder="Consulta sobre servicios marítimos">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted mb-2">Tu Mensaje</label>
                                <textarea name="message" required class="form-control" rows="6" placeholder="Cuéntanos cómo podemos ayudarte..."></textarea>
                            </div>
                            <div class="col-12 text-end mt-5">
                                <button type="submit" class="submit-btn fs-5 shadow-lg">Enviar Mensaje</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
