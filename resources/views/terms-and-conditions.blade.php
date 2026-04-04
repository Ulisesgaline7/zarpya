@extends('layouts.landing.app')

@section('title', translate('messages.terms_and_condition'))

@section('content')
<style>
    .terms-header {
        background: var(--gojek-dark);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
    }
    .terms-section {
        padding: 100px 0;
        background: white;
    }
    .terms-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4b5563;
    }
</style>

<section class="terms-header">
    <div class="container">
        <h1 class="display-font wow fadeInUp" style="font-size: 3.5rem;">{{ translate('messages.terms_and_condition') }}</h1>
    </div>
</section>

<section class="terms-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="terms-content wow fadeInUp">
                    {!! $data !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
