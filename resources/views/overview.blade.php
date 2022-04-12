@extends('layouts.layout')

@section('content')
<div id="app">
    <div class="container pt-3">
        <h1 class="text-center">HomeGrowR Overview</h1>
        <div class="row">
            @for ($i = 0; $i < count($data); ++$i)
                <div class="col-md-6">
                    <chart name={{ $data[$i]['alias'] }} uuid={{ $data[$i]['uuid'] }} ></chart>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
