<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comments;
use App\Models\Report;

class ReportController extends Controller
{
    public function create(Comments $comment)
    {
        // Verificar si ya reportó este comentario
        if ($comment->isReportedBy(auth()->id())) {
            return redirect()
                ->back()
                ->with('warning', 'Vous avez déjà signalé ce commentaire.');
        }

        return view('report.create', compact('comment'));
    }

    public function store(Request $request, Comments $comment)
    {
        if ($comment->isReportedBy(auth()->id())) {
            return redirect()
                ->back()
                ->with('error', 'Vous avez déjà signalé ce commentaire.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Report::create([
            'comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'reason' => $validated['reason'],
            'description' => $validated['description']
        ]);

        return redirect()
            ->route('articles.show', $comment->article_id)
            ->with('success', 'Commentaire signalé avec succès.');
    }

    // Ver todos los reportes admin.
    public function index()
    {
        if (auth()->user()->is_admin !== 'admin') {
            return redirect()->route('welcome')->with('error', 'Accès refusé.');
        }

        $reports = Report::with(['comment.user', 'comment.article', 'isReportedBy'])
            ->paginate(20);

        return view('reports.index', compact('reports'));
    }

    public function show(CommentReport $report)
    {
        if (auth()->user()->is_admin !== 'admin') {
            return redirect()->route('welcome')->with('error', 'Accès refusé.');
        }

        $report->load(['comment.user', 'comment.article', 'reporter', 'reviewer']);

        return view('reports.show', compact('report'));
    }

}