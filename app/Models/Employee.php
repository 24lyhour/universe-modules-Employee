<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\School\Models\School;
use Modules\School\Models\Department;
use Modules\School\Models\Course;
use Modules\Employee\Models\EmployeeType;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Employee types.
     */
    public const TYPE_FULL_TIME = 'full_time';
    public const TYPE_PART_TIME = 'part_time';
    public const TYPE_CONTRACT = 'contract';
    public const TYPE_INTERN = 'intern';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
        'birth_place',
        'current_address',
        'school_id',
        'department_id',
        'position_id',
        'type_employee_id',
        'job_title',
        'employee_type',
        'salary',
        'hire_date',
        'probation_date',
        'probation_end_date',
        'certificate',
        'certificate_image',
        'certificate_code',
        'avatar_url',
        'employee_qr_code',
        'employee_barcode',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_date' => 'date',
        'probation_end_date' => 'date',
        'salary' => 'decimal:2',
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
     * Get the employee's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->first_name . ' ' . $this->last_name),
        );
    }

    /**
     * Get the school that the employee belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the department that the employee belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the courses taught by the employee (as instructor).
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get available employee types.
     */
    public static function getEmployeeTypes(): array
    {
        return [
            self::TYPE_FULL_TIME => 'Full Time',
            self::TYPE_PART_TIME => 'Part Time',
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_INTERN => 'Intern',
        ];
    }

    /**
     * Scope for active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Check if employee is on probation.
     */
    public function isOnProbation(): bool
    {
        if (!$this->probation_end_date) {
            return false;
        }

        return now()->lessThan($this->probation_end_date);
    }

    /**
     * Get the employee type that the employee belongs to.
     */
    public function employeeType(): BelongsTo
    {
        return $this->belongsTo(EmployeeType::class, 'type_employee_id');
    }
}
