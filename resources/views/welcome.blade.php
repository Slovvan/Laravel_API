<div>
@extends('layouts.app')
@section('content')
@guest
    <li>
                <a href="{{route('articles.index')}}">Liste des articles</a>
     </li>
@endguest
@endsection

 <!-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca -->
</div>
