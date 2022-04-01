<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    use HasFactory;
    protected $fillable = ['jwt', 'expire', 'user_id'];
    function user()
    {
        return $this->BelongsTo(User::class);
    }
}
