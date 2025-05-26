<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Import Auth facade


// --- Admin Controllers ---
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\PositionController as AdminPositionController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\WorkScheduleController as AdminWorkScheduleController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\SalaryController as AdminSalaryController;
use App\Http\Controllers\Admin\RewardDisciplineController as AdminRewardDisciplineController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\EmployeeScheduleController;
// --- Employee Controllers ---
use App\Http\Controllers\Employee\EmployeeAttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- Trang chủ ---
// Chuyển hướng người dùng chưa đăng nhập đến trang login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Nếu đã đăng nhập, chuyển đến dashboard
    }
    return redirect()->route('login');
});

// --- Dashboard Chính (Sau khi đăng nhập) ---
Route::get('/dashboard', function () {
    if (Auth::user()->isAdmin()) {
        // return view('admin.dashboard'); // Load view admin dashboard
        return redirect()->route('admin.employees.index'); // Hoặc chuyển hướng đến trang chính của admin
    } elseif (Auth::user()->isEmployee()) {
        // return view('employee.dashboard'); // Load view employee dashboard
        return redirect()->route('employee.attendance.create'); // Hoặc chuyển hướng đến trang chấm công
    } else {
        Auth::logout();
        return redirect('/login');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Nhóm Route cho Admin ---
Route::middleware(['auth', 'admin']) // Yêu cầu đăng nhập và là admin
    ->prefix('admin')           // Thêm tiền tố /admin vào URL
    ->name('admin.')            // Thêm tiền tố admin. vào tên route
    ->group(function () {

        // Quản lý Nhân sự (CRUD)
        Route::resource('employees', AdminEmployeeController::class);

        Route::prefix('employees/{employee}/schedules')->name('employees.schedules.')->group(function () {
            // {employee} sẽ tự động được route model binding
    
            // Danh sách lịch đã gán cho nhân viên này
            Route::get('/', [EmployeeScheduleController::class, 'index'])->name('index');

            // form     tạo lịch mới gán
            Route::get('/create', [EmployeeScheduleController::class, 'create'])->name('create');

            // Lưu lịch mới gán
            Route::post('/', [EmployeeScheduleController::class, 'store'])->name('store');

            // Form sửa lịch đã gán
            // {schedule_assignment} sẽ được binding với model EmployeeSchedule
            Route::get('/{schedule_assignment}/edit', [EmployeeScheduleController::class, 'edit'])->name('edit');

            // Cập nhật lịch đã gán
            Route::put('/{schedule_assignment}', [EmployeeScheduleController::class, 'update'])->name('update');

            // Xóa lịch đã gán
            Route::delete('/{schedule_assignment}', [EmployeeScheduleController::class, 'destroy'])->name('destroy');
        });

        // Quản lý Chức vụ (CRUD)
        Route::resource('positions', AdminPositionController::class);

        // Quản lý Phòng Ban (CRUD)
        Route::resource('departments', AdminDepartmentController::class);

        // Quản lý Ca làm việc (CRUD)
        Route::resource('work-schedules', AdminWorkScheduleController::class);

        // Quản lý Chấm công (Admin xem/sửa)
        Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{attendance}/edit', [AdminAttendanceController::class, 'edit'])->name('attendances.edit');
        Route::put('attendances/{attendance}', [AdminAttendanceController::class, 'update'])->name('attendances.update');
        // Tùy chọn: Route để admin thêm chấm công thủ công
        // Route::post('attendances', [AdminAttendanceController::class, 'store'])->name('attendances.store');
    
        // Quản lý Lương (CRUD)
        Route::resource('salaries', AdminSalaryController::class);
        // Có thể thêm route riêng cho các hành động đặc biệt như 'mark_as_paid' nếu cần
        // Route::patch('salaries/{salary}/pay', [AdminSalaryController::class, 'markAsPaid'])->name('salaries.pay');
    
        // Quản lý Khen thưởng/Kỷ luật (CRUD)
        // Lưu ý: dùng dấu gạch ngang trong URL nhưng resource name là 'rewards-discipline'
        Route::resource('rewards-discipline', AdminRewardDisciplineController::class)
            ->parameters(['rewards-discipline' => 'rewardsDiscipline']); // Ánh xạ parameter
    
        // Quản lý Tài khoản Người dùng (CRUD)
        Route::resource('users', AdminUserController::class);

        // Thống kê & Báo cáo
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminReportController::class, 'index'])->name('index'); // Menu báo cáo
            Route::get('/employee-statistics', [AdminReportController::class, 'employeeStatistics'])->name('employee-statistics');
            Route::get('/attendance-report', [AdminReportController::class, 'attendanceReport'])->name('attendance-report');
            Route::get('/salary-report', [AdminReportController::class, 'salaryReport'])->name('salary-report');
        });

    }); // Kết thúc nhóm route Admin

// --- Nhóm Route cho Employee ---
Route::middleware(['auth', 'employee']) // Yêu cầu đăng nhập và là employee
    ->prefix('employee')             // Thêm tiền tố /employee vào URL
    ->name('employee.')             // Thêm tiền tố employee. vào tên route
    ->group(function () {

        // Chấm công của nhân viên
        Route::get('attendance', [EmployeeAttendanceController::class, 'create'])->name('attendance.create'); // Trang hiển thị nút check-in/out
        Route::post('attendance/check-in', [EmployeeAttendanceController::class, 'storeCheckIn'])->name('attendance.checkin');
        Route::post('attendance/check-out', [EmployeeAttendanceController::class, 'storeCheckOut'])->name('attendance.checkout');
        Route::get('attendance/history', [EmployeeAttendanceController::class, 'history'])->name('attendance.history'); // Trang xem lịch sử chấm công
    
        // Thông tin cá nhân của nhân viên
        Route::get('profile', [EmployeeProfileController::class, 'edit'])->name('profile.edit'); // Trang sửa thông tin
        Route::patch('profile', [EmployeeProfileController::class, 'update'])->name('profile.update'); // Route xử lý cập nhật (PATCH thường dùng cho cập nhật một phần)
    
        // Tùy chọn: Route xem bảng lương cá nhân
        // Route::get('salaries', [EmployeeSalaryController::class, 'index'])->name('salaries.index');
        // Route::get('salaries/{salary}', [EmployeeSalaryController::class, 'show'])->name('salaries.show'); // Chỉ xem lương của mình
    
    }); // Kết thúc nhóm route Employee
// --- Route Logout ---
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
require __DIR__ . '/auth.php';