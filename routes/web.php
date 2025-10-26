<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\AdminPostController;


Route::get('clear-all', function(){
    $commands = [
        'cache:clear',
        'view:clear',
        'route:clear',
        'config:clear',
    ];

    $exitCodes = [];

    foreach ($commands as $command) {
        $exitCodes[$command] = Artisan::call($command);
    }

    return response()->json($exitCodes);
});
Route::get('/migrate-and-seed', function () {
    Artisan::call('migrate:fresh --seed');
    return 'Migration and seeding completed.';
});
Route::get('/storage', function(){
    echo $exitcode = Artisan::call('storage:link');
});

Route::get('/', function () {
    return view('login');
});

Route::controller(UserController::class)->group(function () {
    Route::get('register','register')->name('register');
    Route::post('register.submit','RegisterSubmit')->name('register.submit');
    // Route::get('login','login')->name('login');
    Route::Post('login.check','LoginCheck')->name('login.check');

    Route::get('auth/google','redirectToGoogle')->name('auth/google');
    Route::get('auth/google/callback','handleGoogleCallback')->name('auth/google/callback');

    Route::get('auth/facebook','redirectToFacebook')->name('auth/facebook');
    Route::get('auth/facebook/callback','handleFacebookCallback')->name('auth/facebook/callback');
});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    Route::controller(PostsController::class)->group(function () {
        Route::post('/posts.store','PostStore')->name('posts.store');
        Route::post('/posts.comment/{id}','PostsComment')->name('posts.comment');
        Route::get('/posts.edit/{id}','PostsEdit')->name('posts.edit');
        Route::PUT('/posts.update/{id}','PostUpdate')->name('posts.update');
        Route::delete('/posts.delete/{id}','PostsDelete')->name('posts.delete');
        Route::get('/comment.edit/{id}','CommentEdit')->name('comment.edit');
        Route::PUT('/comment.update/{id}','CommentUpdate')->name('comment.update');
        Route::delete('/comment.delete/{id}','CommentDelete')->name('comment.delete');
    });
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/admin', function () {
    return view('Admin.login');
});
Route::Post('admin.login.check',[AdminController::class,'AdminLoginCheck'])->name('admin.login.check');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/dashboard', 'AdminDashboard')->name('admin.dashboard');
        Route::get('users.list', 'UsersList')->name('users.list');
        Route::get('/admin/users/edit/{id}','AdminUsersEdit')->name('admin.users.edit');
        Route::PUT('/admin/users/update/{id}','AdminUsersUpdate')->name('admin.users.update');
        Route::delete('admin/users/delete/{id}', 'AdminUsersDelete')->name('admin.users.delete');
        Route::get('post/list', 'PostList')->name('post.list');
    });
    Route::controller(AdminPostController::class)->group(function () {
        Route::post('/admin/posts/comment/{id}','AdminPostsComment')->name('admin.posts.comment');
        Route::get('/admin/posts/edit/{id}','AdminPostsEdit')->name('admin.posts.edit');
        Route::PUT('/admin/posts/update/{id}','AdminPostUpdate')->name('admin.posts.update');
        Route::delete('/admin/posts/delete/{id}','AdminPostsDelete')->name('admin.posts.delete');
        Route::get('/admin/comment/edit/{id}','AdminCommentEdit')->name('admin.comment.edit');
        Route::PUT('/admin/comment/update/{id}','AdminCommentUpdate')->name('admin.comment.update');
        Route::delete('/admin/commentdelete/{id}','AdminCommentDelete')->name('admin.comment.delete');
    });
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
