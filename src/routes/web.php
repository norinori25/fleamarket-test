<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\CommentController;
use Stripe\Stripe;

// 一般公開ルート
Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/items', [itemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// ログイン必須ルート
Route::middleware('auth')->group(function () {
    // マイページ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // お気に入り
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{item}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 購入フロー
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item_id}/address', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item_id}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::post('/item/{item_id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::post('/payment', [StripeController::class, 'payment'])->name('purchase.payment');
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel/{item_id}', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout');
});