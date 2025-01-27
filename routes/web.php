<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('home');
Route::get('/choose-outlet', [DashboardController::class, 'chooseOutlet'])->middleware('auth')->name('choose-outlet');
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::resource('/user', UserController::class)->middleware(['auth', 'role:owner']);
Route::post('/user/delete', [UserController::class, 'destroy'])->middleware(['auth', 'role:owner']);
Route::get('/user/edit/{user}', function (User $user) {
    if (!$user->hasRole('owner')) {
        return redirect()->back();
    }
    return view('account.edit', [
        'user' => $user->with('level')->where('id', $user->id)->get()
    ]);
})->middleware(['auth', 'role:owner']);
Route::post('/user/edit/{user}', [UserController::class, 'updateProfile'])->middleware('auth');

Route::resource('/menu', ProductController::class)->middleware('auth');
Route::get('/menus/shows', [ProductController::class, 'show'])->name('menus.show');

Route::resource('/transaction', TransactionController::class)->middleware('auth');

Route::get('/activityLog', [ActivityLogController::class, 'index'])->name('activityLog');

Route::get('/invoice/{transaction}', function (Transaction $transaction) {
    return View('transaction.invoice', [
        'data' => $transaction->with(['transaction_details', 'transaction_details.menu', 'user'])->where('id',$transaction->id)->get()
    ]);
});

Route::get('/report', [ReportController::class, 'index'])->middleware('auth');
