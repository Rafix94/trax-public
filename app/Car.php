<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';
    protected $primaryKey = 'id';
    protected $fillable = [
        'model',
        'make',
        'year',
        'user_id'
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

}
