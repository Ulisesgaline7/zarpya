@extends('layouts.landing.app')

@section('title', translate('Sobre Zarpya - Navegando por Honduras'))

@section('content')
<style>
    .about-header {
        background: linear-gradient(rgba(0,1,0,0.8), rgba(21,38,62,0.9)), url('https://images.unsplash.com/photo-1455516201248-5a10a65649f2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 150px 0 100px;
        text-align: center;
    }
    .about-content-section {
        padding: 120px 0;
        background: white;
    }
    .about-text {
        font-size: 1.25rem;
        line-height: 1.9;
        color: var(--deep-blue);
        opacity: 0.9;
    }
    .maritime-card {
        border-radius: 40px;
        padding: 50px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-top: -80px;
        position: relative;
        z-index: 10;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }
</style>

<section class="about-header">
    <div class="container">
        <span class="maritime-badge" style="background: var(--cyan-tech);">Nuestra Bitácora</span>
        <h1 class="display-font wow fadeInUp" style="font-size: 4rem;">Sobre Zarpya</h1>
        <p class="fs-4 opacity-75 mt-3 wow fadeInUp" data-wow-delay="0.1s">Navegando juntos hacia el progreso de Honduras.</p>
    </div>
</section>

<section class="about-content-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="maritime-card wow fadeInUp">
                    <h2 class="display-font mb-5 text-dark" style="font-size: 2.5rem;">Zarpya: Del verbo <span style="color: var(--cyan-tech);">Zarpar</span></h2>
                    <div class="about-text">
                        <p>En Zarpya, nuestra esencia nace del mar y la determinación de avanzar. "Zarpya" proviene de la acción de <strong>zarpar</strong>: el momento exacto en que una flota inicia su travesía hacia un nuevo destino. Para nosotros, cada pedido es una misión y cada entrega es un puerto alcanzado.</p>
                        
                        <p class="mt-4">Nacimos en Honduras con la visión de crear una red logística imparable. No somos solo una app de delivery; somos la infraestructura digital que conecta a los <strong>Puertos</strong> (nuestros comercios aliados) con los <strong>Zarperos</strong> (nuestros valientes repartidores), llevando soluciones a cada rincón de nuestra tierra catracha.</p>
                        
                        <div class="row g-4 mt-5">
                            <div class="col-md-6">
                                <div class="p-4 rounded-4" style="background: var(--cyan-tech); color: white;">
                                    <h4 class="display-font">Nuestra Misión</h4>
                                    <p class="mb-0">Facilitar la vida de los hondureños a través de una flota tecnológica eficiente, segura y confiable.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4" style="background: var(--digital-purple); color: white;">
                                    <h4 class="display-font">Nuestra Visión</h4>
                                    <p class="mb-0">Ser el puerto principal de servicios en Centroamérica, impulsando el crecimiento de cada negocio anclado a nuestra red.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
