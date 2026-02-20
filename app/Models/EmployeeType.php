<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Employee\Database\Factories\EmployeeTypeFactory;

class EmployeeType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'employees_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'time_start',
        'time_end',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => true,
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): EmployeeTypeFactory
    {
        return EmployeeTypeFactory::new();
    }

    /**
     * Get the employees that belong to this type.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'type_employee_id');
    }
}
