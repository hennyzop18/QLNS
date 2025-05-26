<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
// Có thể cần Form Request cho validation
// use App\Http\Requests\StorePositionRequest;
// use App\Http\Requests\UpdatePositionRequest;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::latest()->paginate(15);
        return view('admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        $validated = $request->validated(); // Lấy dữ liệu đã được validate
        Position::create($validated);

        return redirect()->route('admin.positions.index')->with('success', 'Thêm chức vụ thành công!');
    }

    /**
     * Display the specified resource.
     * Thường không cần trang show riêng cho Position, có thể bỏ qua hoặc redirect về index/edit
     */
    public function show(Position $position)
    {
        return view('admin.positions.show', compact('position')); // Hoặc redirect
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $validated = $request->validated();
        $position->update($validated);

        return redirect()->route('admin.positions.index')->with('success', 'Cập nhật chức vụ thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    // app/Http/Controllers/Admin/PositionController.php
    public function destroy(Position $position)
    {
        // Cân nhắc: Kiểm tra xem có nhân viên nào *đang hoạt động* giữ chức vụ này không?
        // Việc này tùy thuộc vào logic nghiệp vụ của bạn.
        // Ví dụ: Không cho ẩn nếu còn NV active.
        if ($position->employees()->where('status', 'active')->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Không thể ẩn chức vụ đang có nhân viên hoạt động.');
        }

        $position->delete(); // <-- Laravel sẽ tự động soft delete ở đây

        // Thay đổi thông báo
        return redirect()->route('admin.positions.index')
            ->with('success', 'Ẩn chức vụ thành công!'); // Thông báo đã thay đổi
    }
}