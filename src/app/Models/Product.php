<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Favorite;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'brand_name',
        'category_id',
        'status',
        'condition',
        'image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(User $user)
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');

        if ($tab === 'mylist' && auth()->check()) {
        // ログインユーザーが「いいね」した商品だけ取得
        $favorites = auth()->user()->favorites()->with('product')->latest()->get();
        $products = $favorites->pluck('product')->filter();
        } else {
        // 通常のおすすめ商品
        $products = Product::latest()->get();
        }

        return view('products.index', compact('products'));
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
