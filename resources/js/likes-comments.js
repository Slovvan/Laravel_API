/**
 * Likes and Comments Handler
 * Manages like toggles and comment submissions without page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    const articleId = document.querySelector('[data-article-id]')?.getAttribute('data-article-id');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ========================================
    // GESTIÓN DE LIKES
    // ========================================
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
                    // Update like count
                    const likesCount = document.getElementById('likes-count');
                    if (likesCount) {
                        likesCount.textContent = data.likes_count;
                    }

                    // Update heart icon
                    const likeHeart = document.getElementById('like-heart');
                    if (likeHeart) {
                        likeHeart.textContent = data.liked ? '❤️' : '🤍';
                    }

                    // Update likes label (singular/plural)
                    const likesLabel = document.getElementById('likes-label');
                    if (likesLabel) {
                        likesLabel.textContent = data.likes_count === 1 ? ' like' : ' likes';
                    }

                    showNotification(data.liked ? 'Likes actualizados' : 'Like eliminado', 'success');
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                showNotification('Erreur lors du like. Veuillez réessayer.', 'error');
            }
        });
    }

    // ========================================
    // GESTIÓN DE COMENTARIOS
    // ========================================
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
                    // Remove "no comments" message if present
                    const noComments = document.getElementById('no-comments');
                    if (noComments) {
                        noComments.remove();
                    }

                    // Add new comment to list
                    const commentsList = document.getElementById('comments-list');
                    if (commentsList) {
                        const commentDiv = createCommentElement(data.comment);
                        commentsList.appendChild(commentDiv);
                    }

                    // Clear textarea
                    textarea.value = '';
                    showNotification('Comentario añadido correctamente', 'success');
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
                showNotification('Erreur lors de l\'ajout du commentaire.', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        });
    }

    // ========================================
    // GESTIÓN DE ELIMINACIÓN DE COMENTARIOS
    // ========================================
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-delete-comment]')) {
            e.preventDefault();
            const btn = e.target.closest('[data-delete-comment]');
            handleDeleteComment(btn);
        }
    });

    async function handleDeleteComment(btn) {
        const commentId = btn.getAttribute('data-delete-comment');

        if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
            return;
        }

        try {
            const response = await fetch(`/api/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            });

            const data = await response.json();

            if (data && data.success) {
                // Remove comment from DOM
                const commentElement = btn.closest('[data-comment-id]');
                if (commentElement) {
                    commentElement.remove();
                    showNotification('Comentario eliminado', 'success');
                }
            }
        } catch (error) {
            console.error('Error deleting comment:', error);
            showNotification('Erreur lors de la suppression.', 'error');
        }
    }

    /**
     * Create HTML element for new comment
     */
    function createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.setAttribute('data-comment-id', comment.id);
        div.style = 'border-left: 2px solid #ccc; padding: 10px; margin: 10px 0;';

        const userLink = `/profil/${comment.user.id}`;
        let deleteButton = '';

        // Check if user can delete (data attribute)
        const canDeleteBtn = document.querySelector('[data-user-can-delete="true"]');
        if (canDeleteBtn) {
            deleteButton = `<button data-delete-comment="${comment.id}" class="btn btn-danger" style="margin-top: 8px; padding: 5px 10px;">Supprimer</button>`;
        }

        div.innerHTML = `
            <p><strong><a href="${userLink}">${escapeHtml(comment.user.name)}</a></strong> - ${comment.created_at}</p>
            <p>${escapeHtml(comment.content)}</p>
            ${deleteButton}
        `;

        return div;
    }

    /**
     * Escape HTML to prevent XSS
     */
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

    // ========================================
    // BROADCASTING LISTENERS
    // ========================================
    if (window.Echo && articleId) {
        window.Echo.channel(`article.${articleId}`)
            .listen('.article.liked', (e) => {
                // Update likes count when someone else likes
                const likesCount = document.getElementById('likes-count');
                if (likesCount) {
                    likesCount.textContent = e.likes_count;
                }
                showNotification(`${e.user_name} liked this article`, 'info');
            });
    }

    /**
     * Show notification toast
     */
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'info' ? '#2196F3' : '#f44336'};
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