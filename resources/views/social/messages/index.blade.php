@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Mensajes</span>
                    <a href="{{ route('messages.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle"></i> Nueva conversación
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count($users) > 0)
                        <div class="list-group">
                            @foreach($users as $user)
                                <a href="{{ route('messages.show', $user) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($user->profile_picture)
                                                <img src="{{ asset('storage/' .$user->profile_picture) }}" class="img-thumbnail rounded-circle" width="50">
                                            @else
                                                <img src="{{ asset('img/default-user.png') }}" class="img-thumbnail rounded-circle" width="50">
                                            @endif
                                            <span class="ml-2">{{ $user->name }}</span>
                                            <small class="text-muted ml-2">({{ ucfirst($user->role) }})</small>
                                        </div>
                                        
                                        @php
                                            $unreadCount = App\Models\Message::where('sender_id', $user->id)
                                                ->where('receiver_id', Auth::id())
                                                ->whereNull('read_at')
                                                ->count();
                                        @endphp
                                        
                                        @if($unreadCount > 0)
                                            <span class="badge badge-primary badge-pill">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <p>No tienes conversaciones activas</p>
                            <a href="{{ route('messages.create') }}" class="btn btn-outline-primary">
                                Iniciar una nueva conversación
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection