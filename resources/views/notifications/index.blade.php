@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="margin-bottom: 20px;">Notificaciones</h1>
    
    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
            <div style="
                border: 1px solid #a9a9a9;
                padding: 12px;
                margin-bottom: 10px;
                border-left: 3px solid {{ is_null($notification->read_at) ? '#99a0c0' : '#a9a9a9' }};
                background: {{ is_null($notification->read_at) ? '#f8f8f8' : '#fff' }};
            ">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                    <strong>{{ $notification->data['commenter_name'] }} comentó tu artículo</strong>
                    <small style="color: #444;">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                </div>
                
                <p style="margin: 10px 0; color: #111;">
                    <strong>"{{ $notification->data['article_title'] }}"</strong>
                </p>
                
                <p style="margin: 10px 0; color: #444; font-style: italic;">
                    "{{ $notification->data['comment_excerpt'] }}..."
                </p>
                
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="
                            padding: 5px 10px;
                            background: #d6daf0;
                            color: #111;
                            border: 1px solid #99a0c0;
                            border-radius: 0;
                            cursor: pointer;
                        ">Ver artículo</button>
                    </form>
                    
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="
                            padding: 5px 10px;
                            background: #f8d7da;
                            color: #721c24;
                            border: 1px solid #f5c2c7;
                            border-radius: 0;
                            cursor: pointer;
                        ">Eliminar</button>
                    </form>
                </div>
            </div>
        @endforeach
        
        @if($notifications->hasPages())
            <div style="margin-top: 20px;">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px 20px; color: #444;">
            <p style="font-size: 1.1em;">No tienes notificaciones</p>
        </div>
    @endif
</div>
@endsection
