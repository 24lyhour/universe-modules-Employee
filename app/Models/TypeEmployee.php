<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Employee\Models\Employee;
// use Modules\Employee\Database\Factories\TypeEmployeeFactory;

class TypeEmployee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
            'uuid',
            'employee_id',
            'name',
            'description',
            'is_active',

    ];


    /**
     * case 
     */
    protected $casts = [
          'status' => 'boolean',
    ];

    // protected static function newFactory(): TypeEmployeeFactory
    // {
    //     // return TypeEmployeeFactory::new();
    // }

    /**
     * Get the employees that belong to this type.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
