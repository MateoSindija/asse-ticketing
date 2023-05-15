<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\UserController;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/', function () {
    return redirect("/login");
});


Auth::routes();

Route::prefix("/home")->group(function () {
    Route::get("", [TicketsController::class, "index"]);
})->name("home");

Route::prefix("/client")->group(function () {
    Route::post('', [ClientController::class, "store"]);
    Route::get('', [ClientController::class, 'index']);
    Route::get('{client_id}', [ClientController::class, "show"]);
    Route::patch('{client_id}', [ClientController::class, "update"]);
    Route::delete('{client_id}', [ClientController::class, "destroy"]);
});


Route::prefix("/comment")->group(function () {
    Route::post('', [CommentController::class, "store"]);
    //get all comments on ticket
    Route::get('ticket/{ticket_id}', [CommentController::class, 'index']);
    Route::get('{comment_id}', [CommentController::class, "show"]);
    Route::patch('{comment_id}', [CommentController::class, "update"]);
    Route::delete('{comment_id}', [CommentController::class, "destroy"]);
});

Route::prefix("/ticket")->group(function () {
    Route::post('', [TicketsController::class, "store"]);
    Route::any('', [TicketsController::class, 'index']);
    Route::get('{ticket_id}', [TicketsController::class, "show"]);
    Route::patch('{ticket_id}', [TicketsController::class, "update"]);
    Route::delete('{ticket_id}', [TicketsController::class, "destroy"]);
});

Route::prefix("/user")->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::get('{user_id}', [UserController::class, "show"]);
    Route::patch('{user_id}', [UserController::class, "update"]);
});


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
