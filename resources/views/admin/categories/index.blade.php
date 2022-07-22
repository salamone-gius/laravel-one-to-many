{{-- estendo il layout app --}}
@extends('layouts.app')

{{-- scrivo il content --}}
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>All categories</h1>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col" class="text-center">Title</th>
                        <th scope="col" class="text-center">Slug</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{$category->id}}</td>
                            <td>{{$category->name}}</td>
                            <td>{{$category->slug}}</td>
                            <td style="min-width: 350px;">
                                <a href="{{route('admin.categories.show', $category->id)}}" class="btn btn-primary">Show category</a>
                                <a href="{{route('admin.categories.edit', $category->id)}}" class="btn btn-warning">Edit category</a>
                                <form class="d-inline-block" action="{{route('admin.categories.destroy', $category->id)}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" href="{{route('admin.categories.edit', $category->id)}}" class="btn btn-danger">Delete category</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center m-4">
        <a href="{{route('admin.categories.create')}}" class="btn btn-success">Create new category</a>
    </div>
</div>
@endsection
