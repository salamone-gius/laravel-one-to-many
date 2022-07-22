{{-- estendo il layout app --}}
@extends('layouts.app')

{{-- scrivo il content --}}
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>{{$category->id}} - {{$category->name}}</h1>
        </div>
        <div class="card-body">
            {{$category->content}}
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center m-4">
        <a href="{{route('admin.categories.index')}}" class="btn btn-secondary">Return to all categories</a>
    </div>
</div>
@endsection
