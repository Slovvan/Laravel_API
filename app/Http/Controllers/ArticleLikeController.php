<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;


class ArticleLikeController extends Controller
{
    public function toggle(Article $article){
        $user = auth()->user();

        if(!$user){
            return redirect()->route('login')->with('error', 'Utilisateur non trouvé');
        };

        $liked = $user->toggleLikeArticle($article);
        
        if($liked){
            return back()->with('success', 'Article ajouté à favoris');
        }
        return back()->with('info', 'Article rétiré à favoris');
    }
}
