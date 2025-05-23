@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            Conversación con {{ $user->name }}
                            <small class="text-muted">({{ ucfirst($user->role) }})</small>
                        </div>
                        <a href="{{ route('messages.index') }}" class="btn btn-sm btn-secondary">Volver</a>
                    </div>

                    <div class="card-body">
                        <div class="messages-container">
                            @foreach($messages as $message)
                                <div class="message {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                                    <div
                                        class="message-content {{ $message->sender_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }}">
                                        {{ $message->content }}
                                        <small class="message-time d-block">
                                            {{ $message->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>

                            @endforeach
                        </div>

                        <form action="{{ route('messages.store', $user->id) }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            <div class="form-group">
                                <textarea name="content" rows="3"
                                    class="form-control @error('content') is-invalid @enderror"
                                    placeholder="Escribe tu mensaje..."></textarea>
                                @error('content')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .messages-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message-content {
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 70%;
        }

        .message-time {
            font-size: 0.7em;
            margin-top: 5px;
        }
    </style>
@endsection