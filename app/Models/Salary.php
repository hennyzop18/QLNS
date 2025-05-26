<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'base_salary',
        'allowances',
        'deductions',
        'bonus',
        'fines',
        'net_salary',
        'status',
        'paid_date',
        'notes',
    ];

    /**
     * Các thuộc tính nên được ép kiểu.
     *
     * @var array
     */
    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'paid_date' => 'date',
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonus' => 'decimal:2',
        'fines' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    /**
     * Lấy thông tin nhân viên liên quan đến bản ghi lương này.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}