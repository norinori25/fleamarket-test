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
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy(User $user)
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

}
