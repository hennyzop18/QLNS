<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KioskApiController extends Controller
{
    /**
     * Xử lý yêu cầu cấp token từ ứng dụng Desktop Kiosk.
     * Middleware 'verify.office.ip' đã chạy trước khi vào hàm này.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestToken(Request $request)
    {
        // 1. Validate dữ liệu gửi lên từ Desktop App
        $validated = $request->validate([
            'ssid' => 'required|string',
            // 'mac_address' => 'nullable|string', // Tùy chọn: có thể thêm xác thực MAC
        ]);

        $receivedSsid = $validated['ssid'];
        $allowedSsids = config('hr.allowed_office_ssids', []); // Lấy danh sách SSID từ config

        // 2. Kiểm tra xem SSID nhận được có nằm trong danh sách cho phép không
        if (!in_array($receivedSsid, $allowedSsids)) {
            Log::warning('Kiosk token request denied: Invalid SSID.', [
                'received_ssid' => $receivedSsid,
                'request_ip' => $request->ip(),
            ]);
            
            return response()->json([
                'message' => 'Yêu cầu bị từ chối. Thiết bị không kết nối đúng mạng Wi-Fi của văn phòng.',
            ], 403); // 403 Forbidden
        }

        // 3. Nếu mọi thứ hợp lệ, tạo và trả về token
        $token = Str::random(40);
        $cacheKey = 'kiosk_otp_' . $token;
        
        // Lưu token vào cache, hiệu lực 60 giây
        Cache::put($cacheKey, true, now()->addSeconds(3600));

        Log::info('Kiosk token granted successfully.', [
            'ssid' => $receivedSsid,
            'request_ip' => $request->ip(),
        ]);

        return response()->json(['otp_token' => $token]);
    }
}