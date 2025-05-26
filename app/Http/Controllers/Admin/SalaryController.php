<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Employee;
use App\Models\RewardDiscipline; // Để lấy thưởng/phạt
use App\Models\Attendance; // Để lấy thông tin chấm công
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Sử dụng transaction
use App\Http\Requests\StoreSalaryRequest; // Sử dụng Form Request
use App\Http\Requests\UpdateSalaryRequest; // Sử dụng Form Request
use App\Models\WorkSchedule; // Có thể cần để tính công

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Lọc theo kỳ lương (tháng/năm), nhân viên
        $filterMonth = $request->input('month', now()->month);
        $filterYear = $request->input('year', now()->year);
        $filterEmployee = $request->input('employee_id');

        $query = Salary::with('employee')
            ->whereYear('pay_period_start', $filterYear)
            ->whereMonth('pay_period_start', $filterMonth)
            ->latest(); // Sắp xếp theo id giảm dần hoặc ngày tạo

        if ($filterEmployee) {
            $query->where('employee_id', $filterEmployee);
        }

        $salaries = $query->paginate(20);
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.salaries.index', compact('salaries', 'employees', 'filterMonth', 'filterYear', 'filterEmployee'));
    }

    /**
     * Show the form for creating a new resource (Thường là form để chọn kỳ lương).
     */
    public function create()
    {
        // Hiển thị form chọn tháng/năm để tạo bảng lương
        return view('admin.salaries.create');
    }

    /**
     * Store a newly created resource in storage (Xử lý tạo bảng lương cho kỳ đã chọn).
     * Đây là logic phức tạp, cần cẩn thận.
     */
    public function store(StoreSalaryRequest $request)
    {
        $validated = $request->validated();

        $month = $validated['month'];
        $year = $validated['year'];
        $payPeriodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $payPeriodEnd = $payPeriodStart->copy()->endOfMonth();

        // Kiểm tra xem kỳ lương này đã được tạo chưa
        $existingSalary = Salary::whereYear('pay_period_start', $year)
            ->whereMonth('pay_period_start', $month)
            ->exists();
        if ($existingSalary) {
            return redirect()->route('admin.salaries.create')->with('error', "Bảng lương cho tháng {$month}/{$year} đã tồn tại.");
        }

        // Lấy danh sách nhân viên active trong kỳ lương đó (ví dụ: vào làm trước khi kỳ kết thúc và chưa nghỉ việc trước khi kỳ bắt đầu)
        $employees = Employee::where('status', 'active')
            ->where('hire_date', '<=', $payPeriodEnd)
            ->where(function ($query) use ($payPeriodStart) {
                $query->whereNull('termination_date')
                    ->orWhere('termination_date', '>=', $payPeriodStart);
            })
            ->get();

        if ($employees->isEmpty()) {
            return redirect()->route('admin.salaries.create')->with('error', "Không có nhân viên nào đủ điều kiện tính lương cho kỳ {$month}/{$year}.");
        }

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // --- Logic Tính toán lương chi tiết ---

                // 1. Lương cơ bản & Phụ cấp
                // Ưu tiên 1: Lấy từ bảng cấu trúc lương riêng theo chức vụ/nhân viên (phức tạp hơn)
                // $salaryStructure = $employee->activeSalaryStructure; // Cần tạo model/quan hệ này
                // $baseSalary = $salaryStructure->base_salary ?? 0;
                // $allowances = $salaryStructure->allowances ?? 0;

                // Ưu tiên 2: Lấy từ cột trực tiếp trong bảng `employees` (Đơn giản hơn, cần thêm cột)
                $baseSalary = $employee->base_salary ?? 5000000; // Ví dụ lương cơ bản mặc định nếu chưa có
                $allowances = $employee->allowances ?? 0;       // Ví dụ phụ cấp mặc định

                // 2. Tính ngày công thực tế & Lương theo công (Nâng cao)
                $standardWorkDays = 26; // Ví dụ: Số ngày công chuẩn trong tháng
                $actualWorkDays = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$payPeriodStart, $payPeriodEnd])
                    ->whereIn('status', ['present', 'late']) // Chỉ tính ngày đi làm/đi trễ
                    ->count();
                // Tính lương theo ngày công nếu cần
                // $calculatedBaseSalary = ($baseSalary / $standardWorkDays) * $actualWorkDays;
                $calculatedBaseSalary = $baseSalary; // Tạm thời tính full lương

                // 3. Khấu trừ (BHXH, Thuế TNCN...) -> Rất phức tạp, cần cấu hình
                $deductions = 0; // Tạm thời
                // Ví dụ đơn giản: $deductions = $calculatedBaseSalary * 0.105; // 10.5% BHXH etc.

                // 4. Thưởng/Phạt (như cũ)
                $rewards = RewardDiscipline::where('employee_id', $employee->id)/*...*/ ->sum('amount');
                $fines = RewardDiscipline::where('employee_id', $employee->id)/*...*/ ->sum('amount');

                // 5. Tính lương thực nhận
                $netSalary = $calculatedBaseSalary + $allowances - $deductions + $rewards - $fines;

                // 6. Tạo record Salary
                Salary::updateOrCreate( // Dùng updateOrCreate để tránh lỗi nếu chạy lại
                    [
                        'employee_id' => $employee->id,
                        'pay_period_start' => $payPeriodStart->toDateString(),
                        'pay_period_end' => $payPeriodEnd->toDateString(),
                    ],
                    [
                        'base_salary' => $baseSalary, // Lưu lương cơ bản gốc
                        // Có thể thêm cột lương tính theo công: 'calculated_base_salary' => $calculatedBaseSalary,
                        'allowances' => $allowances,
                        'deductions' => $deductions,
                        'bonus' => $rewards,
                        'fines' => $fines,
                        'net_salary' => max(0, $netSalary),
                        'status' => 'pending',
                        'notes' => "Actual work days: $actualWorkDays/$standardWorkDays. Generated: " . now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.salaries.index', ['month' => $month, 'year' => $year])->with('success', "Tạo/Cập nhật bảng lương tháng {$month}/{$year} thành công!");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating/updating salary batch: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->route('admin.salaries.create')->with('error', 'Lỗi tạo bảng lương: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Salary $salary)
    {
        $salary->load('employee'); // Load thông tin nhân viên
        return view('admin.salaries.show', compact('salary'));
    }

    /**
     * Show the form for editing the specified resource.
     * Cho phép sửa chi tiết lương trước khi thanh toán?
     */
    public function edit(Salary $salary)
    {
        if ($salary->status === 'paid') {
            return redirect()->route('admin.salaries.show', $salary)->with('error', 'Không thể sửa bảng lương đã thanh toán.');
        }
        $salary->load('employee');
        return view('admin.salaries.edit', compact('salary'));
    }

    /**
     * Update the specified resource in storage.
     * Chủ yếu dùng để cập nhật chi tiết lương hoặc đánh dấu đã thanh toán.
     */
    public function update(UpdateSalaryRequest $request, Salary $salary)
    {
        $validated = $request->validated(); // Lấy dữ liệu đã validate

        // Xử lý đánh dấu 'paid'
        if ($request->has('mark_as_paid')) {
            $salary->update([
                'status' => 'paid',
                'paid_date' => now()->toDateString(),
                'notes' => ($salary->notes ?? '') . "\nMarked paid: " . now(),
            ]);
            return redirect()->route('admin.salaries.show', $salary)->with('success', 'Đã đánh dấu thanh toán.');
        }

        // Xử lý hủy 'cancelled' (thêm nút bấm riêng trong view)
        if ($request->has('mark_as_cancelled')) {
            if ($salary->status === 'paid') {
                return redirect()->route('admin.salaries.show', $salary)->with('error', 'Không thể hủy lương đã thanh toán.');
            }
            $salary->update([
                'status' => 'cancelled',
                'notes' => ($salary->notes ?? '') . "\nMarked cancelled: " . now(),
            ]);
            return redirect()->route('admin.salaries.index')->with('success', 'Đã hủy bỏ bảng lương.');
        }


        // Xử lý cập nhật chi tiết (nếu được phép)
        if (empty($validated)) { // Nếu request chỉ chứa mark_as_paid/cancelled đã xử lý ở trên
            return redirect()->route('admin.salaries.show', $salary);
        }

        // Tính lại net_salary dựa trên dữ liệu validated
        $validated['net_salary'] = max(
            0,
            ($validated['base_salary'] ?? $salary->base_salary) +
            ($validated['allowances'] ?? $salary->allowances) -
            ($validated['deductions'] ?? $salary->deductions) +
            ($validated['bonus'] ?? $salary->bonus) -
            ($validated['fines'] ?? $salary->fines)
        );
        $validated['notes'] = ($validated['notes'] ?? $salary->notes) . "\nAdmin updated: " . now();

        $salary->update($validated);

        return redirect()->route('admin.salaries.show', $salary)->with('success', 'Cập nhật chi tiết lương thành công.');
    }


    /**
     * Remove the specified resource from storage.
     * Có nên cho xóa record lương không? Hay chỉ nên cancel?
     */
    public function destroy(Salary $salary)
    {
        if ($salary->status === 'paid') {
            return redirect()->route('admin.salaries.index')->with('error', 'Không thể xóa bảng lương đã thanh toán.');
        }

        // Thay vì xóa, có thể cập nhật status thành 'cancelled'
        // $salary->update(['status' => 'cancelled', 'notes' => ($salary->notes ?? '') . "\nCancelled by admin on " . now()->toDateTimeString()]);
        // return redirect()->route('admin.salaries.index')->with('success', 'Đã hủy bỏ bảng lương.');

        // Hoặc xóa hẳn
        $salary->delete();
        return redirect()->route('admin.salaries.index')->with('success', 'Xóa bảng lương thành công.');
    }
}