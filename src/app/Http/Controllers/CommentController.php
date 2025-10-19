<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $product_id)
    {
        // 未ログインならログインページへリダイレクト
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'コメントするにはログインが必要です。');
        }

        // 入力チェック
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // 該当商品が存在するか確認（存在しないIDを防止）
        $product = Product::findOrFail($product_id);

        // コメント作成
        Comment::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'content'    => $request->content,
        ]);

         // ここで show() にリダイレクトして最新の comments_count を取得
        return redirect()->route('products.show', $product_id)->with('success', 'コメントを投稿しました！');
    }
}
