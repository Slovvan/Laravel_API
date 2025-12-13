@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Signaler un commentaire</h2>
        </div>
        <div>
            <form action="{{ route('comments.report.store', $comment) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="reason" ">Raison*</label>
                    <textarea 
                            name="reason" 
                            id="reason" 
                            rows="1" 
                            class="form-control @error('reason') is-invalid @enderror"
                            placeholder="Écrivez la reason">{{ old('reason') }}</textarea>
                        @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description">Description (optionnel)</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="4" 
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Donnez plus de détails sur le problème...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <a href="{{ route('articles.show', $comment->article_id) }}">Annuler</a>
                    <button type="submit">Signaler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection