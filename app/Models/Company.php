<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';
    protected $fillable = [
        'name', 'logo'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get all of the teams for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class, 'company_id', 'id');
    }

    /**
     * Get all of the roles for the Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(Role::class, 'company_id', 'id');
    }
}
