<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    /** Attributes that are mass assinable */
    protected $fillable = ["user_id", "source", "category", "author"];

    protected $casts = [
        'source' => 'array',
        'category' => 'array',
        'author' => 'array'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
