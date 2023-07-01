<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';
    protected $fillable = [
        'team_id',
        'role_id',
        'name',
        'email',
        'gender',
        'age',
        'phone',
        'photo',
        'is_verified',
        'verified_at'
    ];

    /**
     * Get the team that owns the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Get the role associated with the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'role_id');
    }
}
