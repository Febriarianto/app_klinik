@extends('pages.master')
@section('css')
@endsection
@section('content')
<div class="col-md-12 col-lg-12 my-2 mx-2">
    <div class="row row-cols-1">
        <div class="overflow-hidden d-slider1 ">
            <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                <li class="swiper-slide card card-slide" data-aos="fade-up">
                    <div class="card-body">
                        <div class="progress-widget">
                            <i class="fa-light fa-user-doctor fa-3x"></i>
                            <div class="progress-detail">
                                <p class="mb-2">Dokter</p>
                                <h4 class="counter">{{ $data['countDokter'] ?? '' }}</h4>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="swiper-slide card card-slide" data-aos="fade-up">
                    <div class="card-body">
                        <div class="progress-widget">
                            <i class="fa-light fa-user fa-3x"></i>
                            <div class="progress-detail">
                                <p class="mb-2">Pasien</p>
                                <h4 class="counter">{{ $data['countPasien'] ?? '' }}</h4>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="swiper-slide card card-slide" data-aos="fade-up">
                    <div class="card-body">
                        <div class="progress-widget">
                            <i class="fa-light fa-kit-medical fa-3x"></i>
                            <div class="progress-detail">
                                <p class="mb-2">Perawatan</p>
                                <h4 class="counter">{{ $data['countPerawatan'] ?? '' }}</h4>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="swiper-button swiper-button-next"></div>
            <div class="swiper-button swiper-button-prev"></div>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection