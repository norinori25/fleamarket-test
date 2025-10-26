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
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'コメントするにはログインが必要です。');
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $item = Item::findOrFail($item_id);

        Comment::create([
            'user_id'    => auth()->id(),
            'item_id' => $item->id,
            'content'    => $request->content,
            'is_admin'  => auth()->id() === $item->user_id,
        ]);

        return redirect()->route('items.show', ['item_id' => $item_id])->with('success', 'コメントを投稿しました！');

    }
}
