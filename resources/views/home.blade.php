@extends('layouts.landing.app')
@php($business_name = \App\CentralLogics\Helpers::get_settings('business_name'))
@section('title', translate('Zarpya - La Super App de Honduras'))
@section('content')

<style>
    .hero-section {
        background: linear-gradient(rgba(0,1,0,0.7), rgba(21,38,62,0.8)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        align-items: center;
        color: white;
        text-align: center;
        position: relative;
    }

    .hero-title {
        font-size: 5rem;
        line-height: 1.1;
        max-width: 1000px;
        margin: 0 auto;
        color: var(--white);
        text-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .hero-title span {
        color: var(--cyan-tech);
    }

    .section-padding {
        padding: 120px 0;
    }

    .intro-section {
        text-align: center;
        background: var(--white);
    }

    .maritime-badge {
        display: inline-block;
        padding: 10px 25px;
        border-radius: 100px;
        background: var(--cyan-tech);
        color: var(--white);
        font-weight: 700;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .scale-card {
        border-radius: 35px;
        padding: 50px;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }

    .scale-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 50px rgba(47,185,203,0.15);
    }

    .bg-cyan-light { background-color: #E6F7F9; }
    .bg-purple-light { background-color: #F0F0F9; }
    .bg-blue-light { background-color: #E8EEF5; }
    .bg-green-light { background-color: #E9F6ED; }

    .reward-section {
        background: var(--cyan-tech);
        color: white;
        border-radius: 60px;
        margin: 60px 30px;
        overflow: hidden;
        position: relative;
    }

    .reward-content {
        padding: 100px;
        z-index: 2;
        position: relative;
    }

    .partner-section {
        background: var(--black-pure);
        color: white;
        padding: 120px 0;
    }

    .partner-card {
        background: #0A1118;
        border-radius: 40px;
        padding: 45px;
        height: 100%;
        border: 1px solid rgba(47,185,203,0.1);
        transition: all 0.3s ease;
    }

    .partner-card:hover {
        border-color: var(--cyan-tech);
        background: #0D161F;
    }

    .partner-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 30px;
        filter: drop-shadow(0 5px 10px rgba(47,185,203,0.3));
    }

    .impact-section {
        background: var(--digital-purple);
        color: white;
        border-radius: 60px;
        margin: 60px 30px;
        text-align: center;
        padding: 120px 0;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 3rem;
        }
        .reward-content {
            padding: 50px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1 class="hero-title wow fadeInUp">
            Es momento de <span>Zarpar</span> con la Super App de Honduras
        </h1>
        <p class="fs-4 mt-4 opacity-90 wow fadeInUp" data-wow-delay="0.1s">Zarpya: Tu flota personal para envíos, comida y mucho más.</p>
        <div class="mt-5 wow fadeInUp" data-wow-delay="0.2s">
            <a href="#puertos" class="cmn--btn px-5 py-3 fs-5">Descubre nuestros Puertos</a>
        </div>
    </div>
</section>

<!-- Intro Section -->
<section class="intro-section section-padding">
    <div class="container">
        <span class="maritime-badge">Bienvenido a Bordo</span>
        <h2 class="display-font mb-4 wow fadeInUp" style="font-size: 3rem;">Zarpya: Navegando por todo Honduras</h2>
        <p class="text-muted fs-5 max-w-700 mx-auto wow fadeInUp" data-wow-delay="0.1s">
            Desde las costas hasta la capital, conectamos a miles de hondureños con los mejores Puertos y Zarperos del país.
        </p>
        
        <div class="row mt-5 pt-4">
            <div class="col-md-3 mb-4">
                <div class="wow fadeInUp" data-wow-delay="0.1s">
                    <div class="contact-icon mx-auto mb-4" style="background: var(--cyan-tech); width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="fas fa-ship"></i>
                    </div>
                    <h5>Flota Imparable</h5>
                    <p class="small text-muted">Zarperos listos para llevar tus pedidos a toda velocidad.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="wow fadeInUp" data-wow-delay="0.2s">
                    <div class="contact-icon mx-auto mb-4" style="background: var(--digital-purple); width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="fas fa-anchor"></i>
                    </div>
                    <h5>Puertos Seguros</h5>
                    <p class="small text-muted">Los mejores negocios locales anclados en nuestra plataforma.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="wow fadeInUp" data-wow-delay="0.3s">
                    <div class="contact-icon mx-auto mb-4" style="background: var(--green-success); width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h5>Ruta Confiable</h5>
                    <p class="small text-muted">Seguimiento en tiempo real de tu travesía.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="wow fadeInUp" data-wow-delay="0.4s">
                    <div class="contact-icon mx-auto mb-4" style="background: var(--deep-blue); width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h5>Todo Honduras</h5>
                    <p class="small text-muted">Llegamos a cada rincón del territorio nacional.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scale Section -->
<section id="puertos" class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="maritime-badge">Nuestra Bitácora</span>
            <h2 class="display-font" style="font-size: 3rem;">El impacto de nuestra marea</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="scale-card bg-cyan-light wow fadeInUp" data-wow-delay="0.1s">
                    <h2 class="display-font color-cyan" style="color: var(--cyan-tech);">10M+</h2>
                    <p class="fw-bold mb-0">Zarpadas Exitosas</p>
                    <p class="small text-muted">Entregas completadas con éxito.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="scale-card bg-purple-light wow fadeInUp" data-wow-delay="0.2s">
                    <h2 class="display-font" style="color: var(--digital-purple);">50K+</h2>
                    <p class="fw-bold mb-0">Zarperos</p>
                    <p class="small text-muted">Repartidores activos en las calles.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="scale-card bg-blue-light wow fadeInUp" data-wow-delay="0.3s">
                    <h2 class="display-font" style="color: var(--deep-blue);">15K+</h2>
                    <p class="fw-bold mb-0">Puertos</p>
                    <p class="small text-muted">Negocios que confían en nosotros.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="scale-card bg-green-light wow fadeInUp" data-wow-delay="0.4s">
                    <h2 class="display-font" style="color: var(--green-success);">18</h2>
                    <p class="fw-bold mb-0">Departamentos</p>
                    <p class="small text-muted">Presencia en todo el país.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Join Us Section (Zarperos & Puertos) -->
<section class="partner-section">
    <div class="container">
        <h2 class="display-font text-center mb-5" style="font-size: 3.5rem;">¡Únete a la tripulación de Zarpya!</h2>
        <div class="row g-4">
            <div class="col-md-6 mb-4">
                <div class="partner-card wow fadeInLeft">
                    <div class="partner-icon" style="background: var(--cyan-tech); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-motorcycle fa-2x text-white"></i>
                    </div>
                    <h3 class="display-font mb-3">Conviértete en Zarpero</h3>
                    <p class="text-white-50 fs-5 mb-4">Sé el dueño de tu tiempo y genera ingresos navegando por la ciudad. ¡Zarpa hoy mismo!</p>
                    <a href="{{ route('deliveryman.create') }}" class="cmn--btn px-5 py-3 fs-5">Inscribir mi Flota</a>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="partner-card wow fadeInRight">
                    <div class="partner-icon" style="background: var(--digital-purple); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-store fa-2x text-white"></i>
                    </div>
                    <h3 class="display-font mb-3">Registra tu Puerto</h3>
                    <p class="text-white-50 fs-5 mb-4">Haz que tu negocio llegue a miles de clientes. Ancla tu comercio en la red de Zarpya.</p>
                    <a href="{{ route('restaurant.create') }}" class="cmn--btn px-5 py-3 fs-5">Abrir mi Puerto</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Section -->
<section class="impact-section wow zoomIn">
    <div class="container">
        <h2 class="display-font mb-4" style="font-size: 3.5rem;">Navegando hacia un futuro mejor</h2>
        <div class="max-w-700 mx-auto mb-5">
            <p class="fs-4 opacity-90">En Zarpya creemos en el talento hondureño y en la tecnología que conecta sueños con realidades.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80" class="img-fluid rounded-pill shadow-lg border border-5 border-white" style="max-height: 400px; width: 100%; object-fit: cover;" alt="Zarpya Team">
            </div>
        </div>
    </div>
</section>

<!-- Download App -->
<section class="download-section section-padding">
    <div class="container text-center">
        <h2 class="display-font mb-4" style="font-size: 3rem;">¿Listo para la travesía? <br>Descarga la App</h2>
        <div class="d-flex justify-content-center gap-4 mt-5">
            <a href="{{ $landing_data['seller_app_earning_links']['playstore_url'] ?? '#' }}" class="wow bounceIn">
                <img src="{{ asset('/public/assets/landing/img/google-play.png') }}" height="70" alt="Zarpya Google Play">
            </a>
            <a href="{{ $landing_data['seller_app_earning_links']['apple_store_url'] ?? '#' }}" class="wow bounceIn" data-wow-delay="0.1s">
                <img src="{{ asset('/public/assets/landing/img/apple-store.png') }}" height="70" alt="Zarpya App Store">
            </a>
        </div>
    </div>
</section>

@endsection
