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
     * KIỂM TRA VẮNG MẶT (ABSENT)
     * Trả về true nếu thời gian check-in trễ hơn mốc cho phép đi trễ.
     *
     * @param Carbon $checkInTime
     * @return boolean
     */
    public function isAbsent(Carbon $checkInTime): bool
    {
        // Nếu không có mốc đi trễ, coi như không bao giờ vắng (chỉ có trễ)
        if (!$this->late_threshold) {
            return false;
        }

        $thresholdTime = Carbon::parse($checkInTime->toDateString() . ' ' . $this->late_threshold);

        return $checkInTime->gt($thresholdTime);
    }

    /**
     * KIỂM TRA ĐI TRỄ (LATE)
     * Trả về true nếu check-in trong khoảng (start_time, late_threshold].
     *
     * @param Carbon $checkInTime
     * @return boolean
     */
    public function isLate(Carbon $checkInTime): bool
    {
        $startTime = Carbon::parse($checkInTime->toDateString() . ' ' . $this->start_time);
        $thresholdTime = $this->late_threshold 
            ? Carbon::parse($checkInTime->toDateString() . ' ' . $this->late_threshold)
            : $startTime; // Nếu không có threshold, không bao giờ trễ

        // Đi trễ là khi check-in sau giờ bắt đầu VÀ không muộn hơn mốc đi trễ
        return $checkInTime->gt($startTime) && $checkInTime->lte($thresholdTime);
    }

    /**
     * KIỂM TRA ĐÚNG GIỜ (PRESENT)
     * Trả về true nếu check-in trong khoảng [start_time - 5 phút, start_time].
     *
     * @param Carbon $checkInTime
     * @return boolean
     */
    public function isPresent(Carbon $checkInTime): bool
    {
        $startTime = Carbon::parse($checkInTime->toDateString() . ' ' . $this->start_time);
        $earlyMargin = $startTime->copy()->subMinutes(5); // Cho phép đến sớm 5 phút

        // Đúng giờ là khi check-in nằm trong khoảng cho phép
        return $checkInTime->between($earlyMargin, $startTime);
    }
    
    public function isLeftEarly(Carbon $checkOutTime): bool // **** THÊM PHƯƠNG THỨC NÀY ****
    {
        // So sánh giờ check-out với giờ kết thúc của lịch
        return $checkOutTime->format('H:i:s') < Carbon::parse($this->end_time)->format('H:i:s');
    }

    
    /**
     * Accessor để tính toán và lấy ra số giờ làm việc của một ca.
     * Tự động có thể gọi qua $schedule->duration_in_hours
     */
   
    public function getDurationInHoursAttribute(): float
{
    $startTime = Carbon::parse($this->start_time);
    $endTime = Carbon::parse($this->end_time);
    
    // Xử lý ca qua đêm (nếu có)
    if ($endTime->lt($startTime)) {
        $endTime->addDay();
    }
    
    // === SỬA ĐỔI QUAN TRỌNG TẠI ĐÂY ===
    // Sử dụng diffInMinutes với tham số thứ hai là `true` để đảm bảo kết quả luôn là số dương (giá trị tuyệt đối).
    $totalMinutes = $startTime->diffInMinutes($endTime, true);
    // ===================================
    
    $totalHours = $totalMinutes / 60;

    // Quy tắc nghiệp vụ: Trừ 1 giờ nghỉ trưa cho ca có tên chứa "Hành chính"
    if (str_contains(strtolower($this->name), 'hành chính')) {
        $breakHours = 1;
        // Chỉ trừ nếu ca làm việc dài hơn giờ nghỉ
        return ($totalHours > $breakHours) ? $totalHours - $breakHours : $totalHours;
    }
    
    // Các ca khác không trừ giờ nghỉ
    return $totalHours;
}
}