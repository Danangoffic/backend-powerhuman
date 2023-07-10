<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';
    protected $fillable = ['name', 'company_id'];

    /**
     * Get the company that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * Get all of the responsibilities for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function responsibilities()
    {
        return $this->hasMany(Responsibility::class, 'role_id', 'id');
    }

    /**
     * Get the employee that owns the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
