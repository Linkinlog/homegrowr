@extends('layouts.layout')

@section('content')
<div id="app" style="padding-bottom: 20px;">
    <div class="container pt-3">
        <h1 class="text-center">HomeGrowR Overview</h1>
        <div class="row">
            @for ($i = 0; $i < count($atmosphere); ++$i)
                <div class="col-md-6">
                    <chart name={{ $atmosphere[$i]['alias'] }} uuid={{ $atmosphere[$i]['uuid'] }} ></chart>
                </div>
            @endfor
            @for ($i = 0; $i < count($cameras); ++$i)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center">{{ $cameras[$i]['alias'] }}</div>
                        <div class="card-body">
                            <img width="100%" height="100%" src="/test" alt="webcam"></img>
                            <video control type="video/mpeg4">
                                <source src="/videos/?ip={{ urlencode($cameras[$i]['ipaddr']) }}">
                            </video>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
