<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Attendance::truncate(); // Xóa dữ liệu cũ nếu cần thiết khi chạy lại seeder

        $employees = Employee::where('status', 'active')->pluck('id'); // Lấy ID nhân viên active
        $startDate = Carbon::now()->subDays(30); // Ví dụ: Tạo dữ liệu cho 30 ngày qua
        $endDate = Carbon::now();

        foreach ($employees as $employeeId) {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if (fake()->boolean(85)) { // 85% đi làm vào ngày đó
                    $startTime = Carbon::parse($date->toDateString() . ' ' . fake()->randomElement(['08:00:00', '07:50:00', '08:10:00', '08:25:00']));
                    $startTime->addMinutes(fake()->numberBetween(-5, 10)); // Thêm/bớt vài phút

                    $endTime = null;
                    if (fake()->boolean(95)) { // 95% đã checkout
                        $endTime = $startTime->copy()->addHours(fake()->numberBetween(8, 9))->addMinutes(fake()->numberBetween(0, 59));
                    }

                    // Sử dụng updateOrCreate để tránh lỗi trùng lặp
                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'date' => $date->toDateString() // Chỉ cần ngày
                        ],
                        [
                            'check_in_time' => $startTime,
                            'check_out_time' => $endTime,
                            // Xác định status dựa trên logic của bạn (ví dụ giờ vào và WorkSchedule)
                            'status' => $endTime ? ($startTime->format('H:i') > '08:15' ? 'late' : 'present') : null,
                            'notes' => fake()->optional(0.05)->sentence()
                        ]
                    );
                }
            }
        }
    }
}