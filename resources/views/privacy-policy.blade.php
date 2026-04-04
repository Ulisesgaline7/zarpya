@extends('layouts.landing.app')

@section('title', translate('messages.privacy_policy'))

@section('content')
<style>
    .policy-header {
        background: var(--gojek-dark);
        color: white;
        padding: 120px 0 80px;
        text-align: center;
    }
    .policy-section {
        padding: 100px 0;
        background: white;
    }
    .policy-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4b5563;
    }
</style>

<section class="policy-header">
    <div class="container">
        <h1 class="display-font wow fadeInUp" style="font-size: 3.5rem;">{{ translate('messages.privacy_policy') }}</h1>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="policy-content wow fadeInUp">
                    {!! $data !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
