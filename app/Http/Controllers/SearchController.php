<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        if (empty($query)) {
            return redirect()->route('articles.index');
        }

        return redirect()->route('articles.index', ['q' => $query]);
    }
}
