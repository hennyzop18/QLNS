<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::latest()->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name|max:255',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Thêm phòng ban thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('departments')->ignore($department->id)],
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Cập nhật phòng ban thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    // app/Http/Controllers/Admin/DepartmentController.php
    public function destroy(Department $department)
    {
        // Kiểm tra xem có nhân viên nào (kể cả đã nghỉ) đang thuộc phòng ban này không?
        // Nếu bạn chỉ muốn kiểm tra NV đang hoạt động, thêm ->where('status', 'active')
        if ($department->employees()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Không thể ẩn phòng ban đang có nhân viên (kể cả đã nghỉ). Hãy chuyển nhân viên sang phòng ban khác trước.');
            // Hoặc nếu chỉ check NV active:
            // ->with('error', 'Không thể ẩn phòng ban đang có nhân viên hoạt động.');
        }

        $department->delete(); // <-- Tự động soft delete

        return redirect()->route('admin.departments.index')
            ->with('success', 'Ẩn phòng ban thành công!'); // <-- Đổi thông báo
    }
}