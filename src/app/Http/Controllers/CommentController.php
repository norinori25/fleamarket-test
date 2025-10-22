<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
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
        $item = Item::findOrFail($item_id);

        // コメント作成
        Comment::create([
            'user_id'    => auth()->id(),
            'item_id' => $item->id,
            'content'    => $request->content,
        ]);

        return redirect()->route('items.show', ['item_id' => $item_id])->with('success', 'コメントを投稿しました！');

    }
}
