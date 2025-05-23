@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Foros - {{ ucfirst(Auth::user()->role) }}</span>
                    <a href="{{ route('forums.create') }}" class="btn btn-sm btn-primary">Crear Foro</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(count($forums) > 0)
                        <div class="list-group">
                            @foreach($forums as $forum)
                                <a href="{{ route('forums.show', $forum) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $forum->title }}</h5>
                                        <small>{{ $forum->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($forum->description, 150) }}</p>
                                    <small>Creado por: {{ $forum->user->name }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No hay foros disponibles para tu rol</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection