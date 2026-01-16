<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Events\ArticleLiked;


class ArticleLikeController extends Controller
{
    public function toggle(Article $article){
        $user = auth()->user();

        if(!$user){
            return redirect()->route('login')->with('error', 'Utilisateur non trouvé');
        };

        $liked = $user->toggleLikeArticle($article);
        
        if($liked){
            broadcast(new ArticleLiked($article, $user))->toOthers();
            return back()->with('success', 'Article ajouté à favoris');
        }
        return back()->with('info', 'Article rétiré à favoris');
    }
}
