<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\FaceDescriptor;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FaceApiController extends Controller
{
    /**
     * Nhân viên đăng ký hoặc cập nhật khuôn mặt duy nhất của họ.
     */
    /**
     * Nhân viên đăng ký khuôn mặt VÀ cập nhật avatar.
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|string',
            'avatar'     => 'required|string', // Nhận ảnh dưới dạng base64
        ]);
        
        $user = Auth::user();
        $employee = $user->employee;

        // 1. Xử lý và lưu ảnh avatar
        try {
            // Tách dữ liệu base64
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->avatar));
            // Tạo tên file duy nhất
            $filename = 'avatars/' . $employee->id . '_' . time() . '.jpg';
            
            // Xóa avatar cũ nếu có
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }

            // Lưu file mới vào storage/app/public/avatars
            Storage::disk('public')->put($filename, $imageData);

            // Cập nhật đường dẫn avatar vào employee
            $employee->update(['avatar' => $filename]);

        } catch (\Exception $e) {
            Log::error('Lỗi lưu avatar: ' . $e->getMessage());
            return response()->json(['message' . 'Đã xảy ra lỗi khi lưu ảnh đại diện.'], 500);
        }

        // 2. Lưu face descriptor
        FaceDescriptor::updateOrCreate(
            ['user_id' => $user->id],
            ['descriptor' => $request->descriptor]
        );

        return response()->json([
            'message' => 'Đăng ký/Cập nhật khuôn mặt và avatar thành công!',
            'new_avatar_url' => Storage::url($employee->avatar), // Trả về URL mới để JS cập nhật
        ]);
    }


    /**
     * Lấy danh sách khuôn mặt đã đăng ký (mỗi người 1 khuôn mặt).
     */
    public function getFaceDescriptors()
    {
        $allDescriptors = FaceDescriptor::with('user.employee:id,first_name,last_name')->get();

        $dataForJs = $allDescriptors->map(function ($item) {
            $user = $item->user;
            if (!$user || !$user->employee) return null;

            return [
                'label'       => (string)$user->id,
                'name'        => $user->employee->full_name,
                'descriptors' => [json_decode($item->descriptor)], 
            ];
        })->filter()->values();

        return response()->json($dataForJs);
    }

    /**
     * Ghi nhận Check-in hoặc Check-out từ Kiosk, có tích hợp logic xác định trạng thái.
     */
    public function recordAttendance(Request $request)
{
    $validated = $request->validate([
        'user_id'   => 'required|exists:users,id',
        'otp_token' => 'required|string',
    ]);

    // --- Kiểm tra Token và Lấy Employee (giữ nguyên) ---
    $tokenKey = 'kiosk_otp_' . $validated['otp_token'];
    if (!Cache::has($tokenKey)) {
        return response()->json(['message' => 'Phiên chấm công không hợp lệ hoặc đã hết hạn.'], 403);
    }
    Cache::forget($tokenKey);
    $user = User::find($validated['user_id']);
    if (!$user || !$user->employee_id) {
        return response()->json(['message' => 'Tài khoản không được liên kết với nhân viên nào.'], 404);
    }
    
    $employee = $user->employee;
    $now = Carbon::now();
    $today = $now->copy()->startOfDay();

    $todaysAttendance = Attendance::where('employee_id', $employee->id)
        ->whereDate('date', $today->toDateString())
        ->first();

    // --- Xử lý Check-out (giữ nguyên) ---
    if ($todaysAttendance && $todaysAttendance->check_in_time && !$todaysAttendance->check_out_time) {
        $todaysAttendance->update(['check_out_time' => $now]);
        $action = 'Check-out';
    } 
    // --- Xử lý Check-in với LOGIC MỚI ---
    else {
        $workSchedule = $employee->activeWorkScheduleOn($today);
        $status = 'present'; // Mặc định là 'present' nếu không có lịch
        
        if ($workSchedule) {
            // Kiểm tra theo thứ tự ưu tiên: Vắng -> Trễ -> Đúng giờ
            if ($workSchedule->isAbsent($now)) {
                $status = 'absent';
                Log::info("FaceID Check-in: Employee {$employee->id} marked as ABSENT.", ['check_in' => $now->toTimeString()]);
            }
            elseif ($workSchedule->isLate($now)) {
                $status = 'late';
                Log::info("FaceID Check-in: Employee {$employee->id} marked as LATE.", ['check_in' => $now->toTimeString()]);
            }
            // Mặc định, nếu không vắng và không trễ, coi như là 'present'
            // Bao gồm cả trường hợp đến sớm (trước -5 phút) theo logic hiện tại
            else {
                $status = 'present';
                Log::info("FaceID Check-in: Employee {$employee->id} marked as PRESENT.", ['check_in' => $now->toTimeString()]);
            }
        } else {
            Log::warning("FaceID Check-in: Employee {$employee->id} has no schedule. Defaulted to 'present'.");
        }

        Attendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $today->toDateString()],
            ['check_in_time' => $now, 'status' => $status, 'check_out_time' => null]
        );
        $action = 'Check-in';
    }
    
    return response()->json([
        'message' => "Chào mừng {$employee->full_name}! Ghi nhận {$action} lúc {$now->format('H:i:s')} thành công."
    ]);
}
     /**
     * Kiểm tra trạng thái đăng ký và trả về URL avatar.
     */
    public function getRegistrationStatus(Request $request)
    {
        $user = Auth::user();
        $hasFaceData = Auth::user()->faceDescriptors !== null;
        $avatarUrl = null;

        if ($user->employee && $user->employee->avatar) {
            // Lấy URL đầy đủ của avatar từ storage
            $avatarUrl = Storage::url($user->employee->avatar);
        }

        return response()->json([
            'has_face_data' => $hasFaceData,
            'avatar_url'    => $avatarUrl,
        ]);
    }
}