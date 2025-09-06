<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Category;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'category_id', 
        'type', 
        'amount', 
        'occurred_at', 
        'description'
    ];

    protected $casts = [
        'occurred_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
