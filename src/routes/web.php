<?php

use Laravel\Fortify\Fortify;
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
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;

// ✅ Fortify標準：認証待ちページ
// Fortify標準：認証待ちページ
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// ✅ メールリンクをクリック後の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.edit')->with('status', 'verified');
})->middleware(['auth', 'signed'])->name('verification.verify');


// ✅ メールリンクをクリック後の遷移（email_verified_at 更新）
Route::get('/email/verified', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.edit')->with('status', 'verified');
})->middleware(['auth', 'signed'])->name('verification.done');

// ✅ Fortify にビューを提供
Fortify::verifyEmailView(function () {
    return view('auth.verify-email');
});

// 一般公開ルート
Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

/// ログイン & メール認証済みユーザー用
Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::post('/checkout/{item_id}', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel/{item_id}', [StripeController::class, 'cancel'])->name('stripe.cancel');

    Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout');
});