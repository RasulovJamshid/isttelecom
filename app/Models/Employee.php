<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'passport',
        'surname',
        'middlename',
        'job_title',
        'phone_number',
        'address',
        'company_id'
    ];

    //definning table relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
