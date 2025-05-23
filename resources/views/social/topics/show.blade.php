@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $topic->title }}</h5>
                        <small>
                            Tema en foro: 
                            <a href="{{ route('forums.show', $forum) }}">{{ $forum->title }}</a>
                        </small>
                    </div>
                    <a href="{{ route('forums.show', $forum) }}" class="btn btn-sm btn-secondary">Volver al Foro</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="topic-content mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <strong>{{ $topic->user->name }}</strong>
                                <small class="text-muted">({{ ucfirst($topic->user->role) }})</small>
                            </div>
                            <small class="text-muted">{{ $topic->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="topic-text">
                            {!! nl2br(e($topic->content)) !!}
                        </div>
                    </div>

                    <h6 class="mb-3">{{ $topic->comments->count() }} {{ Str::plural('Comentario', $topic->comments->count()) }}</h6>

                    @foreach($topic->comments as $comment)
                        <div class="comment mb-3 p-3 border-left">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>{{ $comment->user->name }}</strong>
                                    <small class="text-muted">({{ ucfirst($comment->user->role) }})</small>
                                </div>
                                <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="comment-text">
                                {!! nl2br(e($comment->content)) !!}
                            </div>
                        </div>
                    @endforeach

                    <div class="add-comment mt-4">
                        <h6 class="mb-3">Añadir comentario</h6>
                        <form action="{{ route('comments.store', [$forum, $topic]) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="content" rows="3" class="form-control @error('content') is-invalid @enderror" placeholder="Escribe tu comentario..." required></textarea>
                                @error('content')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Comentar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.topic-content {
    background-color: #f8f9fa;
}
.comment {
    border-left: 4px solid #007bff;
    background-color: #f8f9fa;
}
</style>
@endsection