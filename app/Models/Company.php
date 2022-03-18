<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner',
        'address',
        'email',
        'website',
        'phone_number',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    // protected $hidden = [
    // ];


    //definning table relationships
    public function employes()
    {
        return $this->hasMany(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
