<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon để xử lý time

class WorkSchedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_schedules'; // Tên bảng trong database

    /**
     * The attributes that are mass assignable.
     * Các thuộc tính có thể gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_threshold',
    ];

    /**
     * The attributes that should be cast.
     * Các thuộc tính nên được ép kiểu.
     * Lưu ý: Ép kiểu TIME trực tiếp có thể không cần thiết,
     * Laravel thường xử lý tốt. Nhưng nếu cần định dạng cụ thể, có thể dùng.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'start_time' => 'datetime:H:i:s', // Hoặc để mặc định
    //     'end_time' => 'datetime:H:i:s',
    //     'late_threshold' => 'datetime:H:i:s',
    // ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (Quan hệ)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy danh sách nhân viên được gán lịch làm việc này (quan hệ Many-to-Many).
     * Cần tạo bảng trung gian (pivot table) ví dụ: 'employee_work_schedule'
     * với các cột `employee_id` và `work_schedule_id`.
     *
     * public function employees()
     * {
     *     return $this->belongsToMany(Employee::class, 'employee_work_schedule')
     *                 ->withTimestamps(); // Nếu bảng pivot có timestamps
     * }
     */

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (Truy cập & Thiết lập) - Tùy chọn
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy giờ bắt đầu dưới dạng đối tượng Carbon.
     *
     * @param  string|null  $value
     * @return \Carbon\Carbon|null
     */
    // public function getStartTimeAttribute($value): ?Carbon
    // {
    //     return $value ? Carbon::parse($value) : null;
    // }

    /**
     * Lấy giờ kết thúc dưới dạng đối tượng Carbon.
     *
     * @param  string|null  $value
     * @return \Carbon\Carbon|null
     */
    // public function getEndTimeAttribute($value): ?Carbon
    // {
    //     return $value ? Carbon::parse($value) : null;
    // }

    /**
     * Lấy mốc đi trễ dưới dạng đối tượng Carbon.
     *
     * @param  string|null  $value
     * @return \Carbon\Carbon|null
     */
    // public function getLateThresholdAttribute($value): ?Carbon
    // {
    //     return $value ? Carbon::parse($value) : null;
    // }

    /*
    |--------------------------------------------------------------------------
    | Scopes (Phạm vi truy vấn) - Tùy chọn
    |--------------------------------------------------------------------------
    */

    /**
     * Lọc các ca làm việc hành chính (ví dụ).
     *
     * public function scopeAdministrative($query)
     * {
     *     return $query->where('name', 'like', '%Hành chính%');
     * }
     */

    /*
    |--------------------------------------------------------------------------
    | Methods (Phương thức tùy chỉnh) - Tùy chọn
    |--------------------------------------------------------------------------
    */

    /**
     * Kiểm tra xem một thời gian check-in có bị trễ so với lịch này không.
     *
     * @param \Carbon\Carbon $checkInTime Thời gian check-in cần kiểm tra.
     * @return bool True nếu trễ, False nếu không.
     */
    public function isLate(Carbon $checkInTime): bool
    {
        $thresholdTimeStr = $this->late_threshold
            ? Carbon::parse($this->late_threshold)->format('H:i:s')
            : Carbon::parse($this->start_time)->format('H:i:s');
        return $checkInTime->format('H:i:s') > $thresholdTimeStr;
    }
    public function isAbsentByCheckInTime(?Carbon $checkInTime, ?Carbon $checkOutTime = null): bool
    {
        // Nếu không có thời gian check-in hoặc check-out, coi như vắng mặt
        if (is_null($checkInTime) || is_null($checkOutTime)) {
            return true;
        }

        // So sánh giờ check-in với giờ kết thúc của lịch
        return $checkInTime->format('H:i:s') > Carbon::parse($this->end_time)->format('H:i:s');
    }
    public function isLeftEarly(Carbon $checkOutTime): bool // **** THÊM PHƯƠNG THỨC NÀY ****
    {
        // So sánh giờ check-out với giờ kết thúc của lịch
        return $checkOutTime->format('H:i:s') < Carbon::parse($this->end_time)->format('H:i:s');
    }
}