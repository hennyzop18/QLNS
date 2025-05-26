<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
// Import Form Requests nếu tạo (ví dụ bên dưới)
use App\Http\Requests\StoreWorkScheduleRequest;
use App\Http\Requests\UpdateWorkScheduleRequest;


class WorkScheduleController extends Controller
{
    public function index()
    {
        $schedules = WorkSchedule::latest()->paginate(15);
        return view('admin.work_schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.work_schedules.create');
    }

    // Sử dụng Form Request
    public function store(StoreWorkScheduleRequest $request)
    {
        WorkSchedule::create($request->validated());
        return redirect()->route('admin.work-schedules.index')->with('success', 'Thêm ca làm việc thành công!');
    }

    public function show(WorkSchedule $workSchedule)
    {
        // Thường không cần trang show riêng, có thể redirect
        return redirect()->route('admin.work-schedules.edit', $workSchedule);
    }

    public function edit(WorkSchedule $workSchedule)
    {
        return view('admin.work_schedules.edit', compact('workSchedule'));
    }

    // Sử dụng Form Request
    public function update(UpdateWorkScheduleRequest $request, WorkSchedule $workSchedule)
    {
        $workSchedule->update($request->validated());
        return redirect()->route('admin.work-schedules.index')->with('success', 'Cập nhật ca làm việc thành công!');
    }

    public function destroy(WorkSchedule $workSchedule)
    {
        // Cần kiểm tra xem ca làm việc này có đang được gán cho nhân viên nào không
        // (Nếu có bảng pivot employee_work_schedule)
        // if ($workSchedule->employees()->exists()) {
        //     return redirect()->route('admin.work-schedules.index')->with('error', 'Không thể xóa ca làm việc đang được sử dụng.');
        // }

        $workSchedule->delete();
        return redirect()->route('admin.work-schedules.index')->with('success', 'Xóa ca làm việc thành công!');
    }
}