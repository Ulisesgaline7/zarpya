@extends('layouts.admin.app')

@section('title', translate('Configuración_de_Optimización_Media'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('Configuración_del_Negocio') }}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.nav-menu')
        </div>
        <!-- End Page Header -->

        <form action="{{ route('admin.business-settings.update-ad-surge') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <!-- Cloudinary Section -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="tio-image-outlined mr-1"></i>
                                {{ translate('Configuración_Cloudinary') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="input-label">{{ translate('Cloud_Name') }}</label>
                                <input type="text" name="cloudinary_cloud_name" class="form-control" 
                                    value="{{ $data['cloudinary_cloud_name'] ?? '' }}" placeholder="Enter Cloud Name">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('API_Key') }}</label>
                                <input type="text" name="cloudinary_api_key" class="form-control" 
                                    value="{{ $data['cloudinary_api_key'] ?? '' }}" placeholder="Enter API Key">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('API_Secret') }}</label>
                                <input type="password" name="cloudinary_api_secret" class="form-control" 
                                    value="{{ $data['cloudinary_api_secret'] ?? '' }}" placeholder="Enter API Secret">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AWS S3 Section -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="tio-cloud-outlined mr-1"></i>
                                {{ translate('Configuración_Amazon_S3') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="input-label">{{ translate('S3_Bucket_Name') }}</label>
                                <input type="text" name="aws_s3_bucket" class="form-control" 
                                    value="{{ $data['aws_s3_bucket'] ?? '' }}" placeholder="Enter Bucket Name">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('S3_Key') }}</label>
                                <input type="text" name="aws_s3_key" class="form-control" 
                                    value="{{ $data['aws_s3_key'] ?? '' }}" placeholder="Enter S3 Key">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('S3_Secret') }}</label>
                                <input type="password" name="aws_s3_secret" class="form-control" 
                                    value="{{ $data['aws_s3_secret'] ?? '' }}" placeholder="Enter S3 Secret">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('S3_Region') }}</label>
                                <input type="text" name="aws_s3_region" class="form-control" 
                                    value="{{ $data['aws_s3_region'] ?? '' }}" placeholder="Enter S3 Region (e.g. us-east-1)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn--container justify-content-end mt-3">
                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                <button type="submit" class="btn btn--primary">{{ translate('messages.save_information') }}</button>
            </div>
        </form>
    </div>
@endsection
