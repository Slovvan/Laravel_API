<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ArticleLikeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;

/* Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
 */

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/aboutMe', function () {
    return view('about_me', ['name' => 'Erik']);
})->name("aboutMe");

// Search route
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Rutas de Autenticación
Route::post('/login', [AuthController::class, 'getLoginForm'])->name("loginStore");
Route::post('/register', [AuthController::class, 'getRegisterForm'])->name("registerStore");
Route::get('/login', function () { return view('login'); })->name('login.form')->middleware('guest');
Route::get('/register', function () { return view('register'); })->name('register.form')->middleware('guest');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Artículos 
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
    Route::post('/articles/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('/articles/{article}/like', [ArticleLikeController::class, 'toggle'])->name('articles.like.toggle');
    Route::get('/comments/edit/{id}', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/comments/{comment}/report', [ReportController::class, 'create'])->name('comments.report.create');
    Route::post('/comments/{comment}/report', [ReportController::class, 'store'])->name('comments.report.store');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
});
    
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');// Cambiar esta línea:
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/profil/{profil}', [ProfilController::class, 'show'])->name('profil.show');

Route::group(['middleware' => 'auth', 'admin'], function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/admin/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

});

require __DIR__.'/settings.php';



//COsas faltantes

/* 
    -Un commentaire peut être signalé par un utilisateur.
    -ne page publique accessible via une URL dédiée.
*/