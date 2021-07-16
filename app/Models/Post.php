<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'post',
        'approved',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    /**
     * Define the relationship with user.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public static function boot()
    {
        parent::boot();

        static::saving(function ($post) {
            $post->user_id = Auth::check() ? Auth::user()->id : 1;
        });
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i');
    }
}
