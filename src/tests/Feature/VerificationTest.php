<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後に認証メールが送信される()
    {
        Notification::fake(); // 通知を偽装

        // 未認証ユーザーを作成
        $user = User::factory()->unverified()->create();

        // 認証メール送信
        $this->actingAs($user)
             ->post('/email/verification-notification');

        // 送信されたことを確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function メール認証リンクをクリックするとプロフィール設定画面に遷移する()
    {
        $user = \App\Models\User::factory()->unverified()->create();

        // 一時的な署名付きURLを作成
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 認証URLにアクセス
        $response = $this->actingAs($user)->get($verificationUrl);

        // プロフィール編集画面にリダイレクトされることを確認
        $response->assertRedirect(route('profile.edit'));

        // メール認証済みになっていることを確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    /** @test */
    public function 認証誘導画面からメール認証サイトに遷移できる()
    {
        $user = User::factory()->unverified()->create();

        // 誘導画面にアクセス
        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200); // 誘導画面が表示される
        $response->assertSee('認証はこちらから'); // ボタンがあることを確認

        // ボタン押下をシミュレート（署名付きURL）
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('profile.edit')); // 認証サイトにリダイレクトされる
    }

  /** @test */
    public function 未認証ユーザーはログイン後にメール認証誘導画面へリダイレクトされる()
    {
        $user = User::factory()->unverified()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertGuest(); // 自動ログアウトを確認
    }

}