import { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Heart } from 'lucide-react';

interface Comment {
    id: number;
    content: string;
    user: {
        id: number;
        name: string;
        avatar?: string;
    };
    created_at: string;
    can_edit: boolean;
    can_delete: boolean;
}

interface Article {
    id: number;
    title: string;
    content: string;
    read_time: number;
    excerpt: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        avatar?: string;
    };
    likes_count: number;
    is_liked: boolean;
    comments: Comment[];
}

interface Props {
    article: Article;
    can_like: boolean;
    can_comment: boolean;
    can_edit: boolean;
}

export default function ShowArticle({ article: initialArticle, can_like, can_comment, can_edit }: Props) {
    const [article, setArticle] = useState(initialArticle);
    const [newComment, setNewComment] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        console.log('Article metrics:', {
            read_time: article.read_time,
            excerpt: article.excerpt,
            word_count: article.content.split(' ').length
        });

        // Listen for real-time like updates
        const channel = window.Echo.channel(`article.${article.id}`);
        channel.listen('.article.liked', (e: any) => {
            setArticle(prev => ({
                ...prev,
                likes_count: e.likes_count,
                is_liked: e.user_id === (window as any).user?.id ? e.is_liked : prev.is_liked
            }));
        });

        return () => {
            channel.stopListening('.article.liked');
        };
    }, [article.id]);

    // Toggle like
    const handleLike = async () => {
        if (!can_like) return;

        try {
            const response = await fetch(`/api/articles/${article.id}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            const data = await response.json();

            if (data.success) {
                setArticle(prev => ({
                    ...prev,
                    is_liked: data.liked,
                    likes_count: data.likes_count,
                }));
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    };

    // Submit comment
    const handleCommentSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!can_comment || !newComment.trim() || isSubmitting) return;

        setIsSubmitting(true);

        try {
            const response = await fetch(`/api/articles/${article.id}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ content: newComment }),
            });

            const data = await response.json();

            if (data.success) {
                setArticle(prev => ({
                    ...prev,
                    comments: [...prev.comments, data.comment],
                }));
                setNewComment('');
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    // Delete comment
    const handleDeleteComment = async (commentId: number) => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) return;

        try {
            const response = await fetch(`/api/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            const data = await response.json();

            if (data.success) {
                setArticle(prev => ({
                    ...prev,
                    comments: prev.comments.filter(c => c.id !== commentId),
                }));
            }
        } catch (error) {
            console.error('Error deleting comment:', error);
        }
    };

    return (
        <AppLayout>
            <Head title={article.title} />

            <div className="mx-auto max-w-4xl p-6">
                {/* Article */}
                <article className="rounded-lg border bg-card p-6 shadow-sm">
                    <h1 className="mb-4 text-3xl font-bold">{article.title}</h1>
                    
                    <div className="mb-4 flex items-center gap-3 text-sm text-muted-foreground">
                        {article.user.avatar && (
                            <img src={`/storage/profiles/thumbnails/${article.user.avatar}`} alt={article.user.name} className="w-8 h-8 rounded-full" />
                        )}
                        <span>Par <span className="font-medium">{article.user.name}</span> • {article.created_at}</span>
                    </div>

                    <div className="prose prose-sm max-w-none dark:prose-invert">
                        <p>{article.content}</p>
                    </div>

                    {/* Like button */}
                    {can_like && (
                        <div className="mt-6 flex items-center gap-2">
                            <Button
                                variant={article.is_liked ? 'default' : 'outline'}
                                size="sm"
                                onClick={handleLike}
                                className="flex items-center gap-2"
                            >
                                <Heart className={article.is_liked ? 'fill-current' : ''} size={16} />
                                {article.likes_count} {article.likes_count === 1 ? 'like' : 'likes'}
                            </Button>
                        </div>
                    )}
                </article>

                {/* Comments section */}
                <div className="mt-8">
                    <h2 className="mb-4 text-2xl font-bold">Commentaires ({article.comments.length})</h2>

                    {/* Comment form */}
                    {can_comment && (
                        <form onSubmit={handleCommentSubmit} className="mb-6 rounded-lg border bg-card p-4">
                            <Textarea
                                value={newComment}
                                onChange={(e) => setNewComment(e.target.value)}
                                placeholder="Ajouter un commentaire..."
                                className="mb-2"
                                rows={3}
                            />
                            <Button type="submit" disabled={isSubmitting || !newComment.trim()}>
                                {isSubmitting ? 'Envoi...' : 'Commenter'}
                            </Button>
                        </form>
                    )}

                    {/* Comments list */}
                    <div className="space-y-4">
                        {article.comments.length === 0 ? (
                            <p className="text-center text-muted-foreground">Aucun commentaire pour le moment</p>
                        ) : (
                            article.comments.map((comment) => (
                                <div key={comment.id} className="rounded-lg border bg-card p-4">
                                    <div className="mb-2 flex items-start justify-between">
                                        <div className="flex items-center gap-3">
                                            {comment.user.avatar && (
                                                <img src={`/storage/profiles/thumbnails/${comment.user.avatar}`} alt={comment.user.name} className="w-6 h-6 rounded-full" />
                                            )}
                                            <div>
                                                <span className="font-medium">{comment.user.name}</span>
                                                <span className="ml-2 text-sm text-muted-foreground">
                                                    {comment.created_at}
                                                </span>
                                            </div>
                                        </div>
                                        {comment.can_delete && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => handleDeleteComment(comment.id)}
                                            >
                                                Supprimer
                                            </Button>
                                        )}
                                    </div>
                                    <p className="text-sm">{comment.content}</p>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}