<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes
use App\Models\WorkSchedule;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes; // Sử dụng SoftDeletes

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'phone_number',
        'personal_email',
        'address',
        'hire_date',
        'termination_date',
        'position_id',
        'work_schedule_id',
        'department_id',
        'status',
    ];

    // Quan hệ: Một Employee thuộc về một Position
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);

    }
    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
    public function activeWorkScheduleOn(Carbon $date): ?WorkSchedule
    {
        $targetDate = $date->toDateString();
        $activeAssignment = $this->employeeSchedules()
            ->where('start_date', '<=', $targetDate)
            ->where(function ($query) use ($targetDate) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $targetDate);
            })
            ->orderBy('start_date', 'desc')
            ->with('workSchedule') // Eager load
            ->first();

        return $activeAssignment?->workSchedule;
    }

    // (Tùy chọn) Phương thức lấy lịch làm việc MẶC ĐỊNH hiện tại (để cập nhật cột employees.work_schedule_id)
    public function getCurrentOrDefaultWorkSchedule(): ?WorkSchedule
    {
        // 1. Ưu tiên lịch đang active hôm nay
        $todaySchedule = $this->activeWorkScheduleOn(Carbon::today());
        if ($todaySchedule) {
            return $todaySchedule;
        }

        // 2. Nếu không có, tìm lịch gần nhất trong tương lai
        $futureAssignment = $this->employeeSchedules()
            ->where('start_date', '>', Carbon::today()->toDateString())
            ->orderBy('start_date', 'asc')
            ->with('workSchedule')
            ->first();
        if ($futureAssignment) {
            return $futureAssignment->workSchedule;
        }

        // 3. Nếu không có cả tương lai, lấy lịch gần nhất trong quá khứ (nếu cần)
        $pastAssignment = $this->employeeSchedules()
            ->orderBy('start_date', 'desc') // Lấy cái mới nhất
            ->with('workSchedule')
            ->first();
        if ($pastAssignment) {
            return $pastAssignment->workSchedule;
        }


        // 4. Nếu không có lịch nào được gán, trả về null (hoặc lịch mặc định của hệ thống)
        // return WorkSchedule::find(DEFAULT_SYSTEM_SCHEDULE_ID);
        return null;
    }
    // Quan hệ: Một Employee có thể có một User account
    public function user()
    {
        return $this->hasOne(User::class); // User có employee_id
    }

    // Quan hệ: Một Employee có nhiều Attendance records
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function employeeSchedules()
    {
        // Một Employee có thể có nhiều bản ghi EmployeeSchedule
        return $this->hasMany(EmployeeSchedule::class);
    }
    // Accessor để lấy tên đầy đủ
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}