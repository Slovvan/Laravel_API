@extends('layouts.app')

@section('content')
    <div data-article-id="{{ $article->id }}">
        <h1>{{ $article->title }}</h1>

        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
        @php $ap = $article->user->profil; @endphp
        <img src="{{ ($ap && $ap->avatar_thumbnail) ? asset('storage/' . $ap->avatar_thumbnail) : (($ap && $ap->avatar) ? asset('storage/' . $ap->avatar) : asset('images/default-avatar.png')) }}" 
             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
        <div>
            <p style="margin: 0;"><strong>Autor:</strong> {{ $article->user->name }}</p>
            <p style="margin: 0; font-size: 0.85em; color: #666;">{{ $article->created_at->format('d/m/Y H:i') }}</p>
        </div>
        </div>
        <p><strong>Autor:</strong> {{ $article->user->name }}</p>
        <p><strong>Date:</strong> {{ $article->created_at->format('d/m/Y H:i') }}</p>

        <div>
            {{ $article->content }}
        </div>

        @auth
            <form id="like-form" action="{{ route('articles.like.toggle', $article )}}" method="POST">
                @csrf
                <button type="submit">
                    <span id="like-heart">@if($article->isArticleLikedByUser(auth()->user()->id))❤️@else🤍@endif</span>
                    <span id="likes-count">{{ $article->likesCount() }}</span>
                    <span id="likes-label">{{ $article->likesCount() == 1 ? ' like' : ' likes'}}</span>
                </button>
            </form>
        @endauth


    @if(auth()->check() && (auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id))
        <a href="{{ route('articles.edit', $article->id) }}">Editar</a>
    @endif

    @if(auth()->check() && auth()->user()->is_admin === 'admin' || auth()->id() === $article->user_id)
        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">Suprimer artícle</button>
        </form>
    @endif

    <a href="{{ route('articles.index') }}">Index</a>

    <hr>

    <h2>Commentaires</h2>

        @auth
            <form id="comment-form" action="{{ route('comments.store', $article->id) }}" method="POST">
                @csrf
                <div>
                    <label for="content">Nouveau comentario:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <button type="submit">Comenter</button>
            </form>
        @else
            <p><a href="{{ route('loginStore') }}">Connectez-vous pour commenter</a></p>
        @endauth

        <div id="comments-list">
    @forelse($article->comments as $comment)
        <div class="comment-item" data-comment-id="{{ $comment->id }}" style="border-left: 2px solid #ccc; padding: 10px; margin: 10px 0;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                @php $cp = $comment->user->profil; @endphp
                <img src="{{ ($cp && $cp->avatar_thumbnail) ? asset('storage/' . $cp->avatar_thumbnail) : (($cp && $cp->avatar) ? asset('storage/' . $cp->avatar) : asset('images/default-avatar.png')) }}" 
                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                
                <p style="margin: 0;">
                    <strong><a href="{{route('profil.show', $comment->user->id)}}">{{ $comment->user->name }}</a></strong> 
                    <span style="color: #888; font-size: 0.8em;">- {{ $comment->created_at->format('d/m/Y H:i') }}</span>
                </p>
            </div>

            <div style="margin-left: 50px;">
                <p>{{ $comment->content }}</p>
                @if(auth()->check() && (auth()->user()->is_admin === 'admin' || auth()->id() === $comment->user->id))
                    <button type="button" data-delete-comment="{{ $comment->id }}" style="padding: 4px 8px; background: #f44336; color: white; border: none; border-radius: 3px; cursor: pointer;">Supprimer</button>
                    <a href="{{ route('comments.edit', $comment->id) }}" style="font-size: 0.9em; margin-left: 10px;">Editar</a>
                @endif
                @auth
                
                @if(auth()->id() !== $comment->user_id)
                    @if($comment->isReportedBy(auth()->id()))
                        <span style="font-size: 0.85em; color: #888; margin-left: 10px; font-style: italic;">
                            Déjà signalé
                        </span>
                    @else
                        <a href="{{ route('comments.report.create', $comment->id) }}" 
                        style="font-size: 0.85em; color: #d32f2f; margin-left: 10px; text-decoration: none;">
                            Signaler
                        </a>
                    @endif
                @endif
            @endauth
            </div>
        </div>
    @empty
        <p id="no-comments">Il n'ya pas de comentarios.</p>
    @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Article ID para el script
    window.articleId = {{ $article->id }};
</script>
<script type="module">
    import('{{ asset('js/likes-comments.js') }}').catch(err => console.log('Script loaded inline'));
    
    // Fallback: ejecutar el script inline
    const articleId = window.articleId || {{ $article->id }};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.addEventListener('DOMContentLoaded', function() {
        // GESTIÓN DE LIKES

        const likeForm = document.getElementById('like-form');
        
        if (likeForm && articleId) {
            likeForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                try {
                    const response = await fetch(`/api/articles/${articleId}/like`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                    });

                    const data = await response.json();

                    if (data && typeof data.likes_count !== 'undefined') {
                        document.getElementById('likes-count').textContent = data.likes_count;
                        document.getElementById('like-heart').textContent = data.liked ? '❤️' : '🤍';
                        document.getElementById('likes-label').textContent = data.likes_count === 1 ? ' like' : ' likes';
                        
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                    showNotification('Error al like', 'error');
                }
            });
        }

        // GESTIÓN DE COMENTARIOS

        const commentForm = document.getElementById('comment-form');
        
        if (commentForm && articleId) {
            commentForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const textarea = document.getElementById('content');
                const content = textarea.value.trim();

                if (!content) {
                    showNotification('El comentario no puede estar vacío', 'error');
                    return;
                }

                const submitBtn = commentForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/api/articles/${articleId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ content }),
                    });

                    const data = await response.json();

                    if (data && data.comment) {
                        const noComments = document.getElementById('no-comments');
                        if (noComments) noComments.remove();

                        const commentsList = document.getElementById('comments-list');
                        if (commentsList) {
                            const commentDiv = createCommentElement(data.comment);
                            commentsList.appendChild(commentDiv);
                        }

                        textarea.value = '';
                    }
                } catch (error) {
                    console.error('Error submitting comment:', error);
                    showNotification('Error al agregar comentario', 'error');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        }

        //ELIMINACIÓN DE COMENTARIOS
      
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-delete-comment]');
            if (btn) {
                e.preventDefault();
                handleDeleteComment(btn);
            }
        });

        async function handleDeleteComment(btn) {
            const commentId = btn.getAttribute('data-delete-comment');

            if (!confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
                return;
            }

            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });

                const data = await response.json();

                if (data && data.success) {
                    const commentElement = btn.closest('[data-comment-id]');
                    if (commentElement) {
                        commentElement.remove();
                        showNotification('Comentario eliminado', 'warning');
                    }
                } else {
                    showNotification('Error al eliminar comentario', 'error');
                }
            } catch (error) {
                console.error('Error deleting comment:', error);
                showNotification('Error al eliminar', 'error');
            }
        }

        function createCommentElement(comment) {
            const div = document.createElement('div');
            div.className = 'comment-item';
            div.setAttribute('data-comment-id', comment.id);
            div.style = 'border-left: 2px solid #ccc; padding: 10px; margin: 10px 0;';

            // Lógica para la imagen (si tu API devuelve profil.avatar_thumbnail)
            const avatarUrl = comment.user.profil && comment.user.profil.avatar_thumbnail 
                ? `/storage/${comment.user.profil.avatar_thumbnail}` 
                : '/images/default-avatar.png';

            const currentUserId = {{ auth()->id() ?? 'null' }};
            const isAdmin = {{ auth()->check() && auth()->user()->is_admin === 'admin' ? 'true' : 'false' }};
            
            let actionButtons = '';
            if (currentUserId && (currentUserId == comment.user_id || isAdmin)) {
                actionButtons = `
                    <button type="button" data-delete-comment="${comment.id}" style="padding: 4px 8px; background: #f44336; color: white; border: none; border-radius: 3px; cursor: pointer;">Supprimer</button>
                    <a href="/comments/edit/${comment.id}" style="font-size: 0.9em; margin-left: 10px;">Editar</a>
                `;
            }

            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <img src="${avatarUrl}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    <p style="margin: 0;">
                        <strong><a href="/profil/${comment.user.id}">${escapeHtml(comment.user.name)}</a></strong> 
                        <span style="color: #888; font-size: 0.8em;">- Justo ahora</span>
                    </p>
                </div>
                <div style="margin-left: 50px;">
                    <p>${escapeHtml(comment.content)}</p>
                    ${actionButtons}
                </div>
            `;

            return div;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${type === 'success' ? '#4CAF50' : '#f44336'};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 9999;
                opacity: 0;
                transition: opacity 0.3s;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 10);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
    });
</script>
@endpush
