@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $forum->title }}</h5>
                        <small>Foro para {{ $forum->role_access == 'all' ? 'todos' : ucfirst($forum->role_access) . 's' }}</small>
                    </div>
                    <div>
                        <a href="{{ route('topics.create', $forum) }}" class="btn btn-sm btn-primary">Nuevo Tema</a>
                        <a href="{{ route('forums.index') }}" class="btn btn-sm btn-secondary ml-2">Volver</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="forum-description mb-4">
                        <p>{{ $forum->description }}</p>
                        <small class="text-muted">Creado por {{ $forum->user->name }} el {{ $forum->created_at->format('d/m/Y') }}</small>
                    </div>

                    <h6 class="mb-3">Temas en este foro</h6>

                    @if(count($forum->topics) > 0)
                        <div class="list-group">
                            @foreach($forum->topics as $topic)
                                <a href="{{ route('topics.show', [$forum, $topic]) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $topic->title }}</h6>
                                        <small>{{ $topic->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($topic->content, 100) }}</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Por: {{ $topic->user->name }}</small>
                                        <small>{{ $topic->comments->count() }} {{ Str::plural('comentario', $topic->comments->count()) }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No hay temas en este foro. ¡Sé el primero en crear uno!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection