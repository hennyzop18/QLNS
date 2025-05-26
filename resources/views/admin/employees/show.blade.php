<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi Tiết Nhân Viên') }}
            </h2>
            <div class="flex items-center flex-row justify-between gap-4">
                <a href="{{ route('admin.employees.index') }}">
                    <x-secondary-button>
                        ← {{ __('Danh sách Nhân viên') }}
                    </x-secondary-button>
                </a>
                <a href="{{ route('admin.employees.schedules.index', $employee) }}">
                    <x-secondary-button>
                        {{ __('Quản lý Lịch làm việc') }}
                    </x-secondary-button>
                </a>
                <a href="{{ route('admin.employees.edit', $employee->id) }}">
                    <x-primary-button> {{-- Hoặc dùng icon bút chì --}}
                        {{ __('Sửa Thông Tin') }}
                    </x-primary-button>
                </a>

            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">

                    {{-- Hàng Trên: Ảnh đại diện và Thông tin cá nhân chi tiết --}}
                    <div class="space-y-6"></div>
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $employee->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $employee->employee_code }}</p>
                        {{-- Status Badge --}}
                        @php
                            $statusClass = match ($employee->status) {
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-yellow-100 text-yellow-800',
                                'terminated' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $statusText = match ($employee->status) {
                                'active' => 'Hoạt động',
                                'inactive' => 'Tạm nghỉ',
                                'terminated' => 'Đã nghỉ việc',
                                default => ucfirst($employee->status),
                            };
                        @endphp
                        <span class="mt-2 px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <div class="border-t pt-6 space-y-4">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Thông tin liên hệ
                        </h4>
                        <div class="flex items-center space-x-2 text-sm text-gray-700">
                            <x-icon.phone class="h-5 w-5 text-gray-400" />
                            <span>{{ $employee->phone_number ?? 'Chưa cập nhật' }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-700">
                            <x-icon.email class="h-5 w-5 text-gray-400" />
                            <span>{{ $employee->personal_email ?? 'Chưa cập nhật' }} (Cá nhân)</span>
                        </div>
                        @if($employee->user)
                            <div class="flex items-center space-x-2 text-sm text-gray-700">
                                <x-icon.login class="h-5 w-5 text-gray-400" />
                                <span>{{ $employee->user->email }} (Đăng nhập)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">Thông tin cá nhân</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Ngày sinh</dt>
                            <dd class="mt-1 text-gray-800">
                                {{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d/m/Y') : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Giới tính</dt>
                            <dd class="mt-1 text-gray-800">
                                @if($employee->gender == 'male') Nam
                                @elseif($employee->gender == 'female') Nữ
                                @else Khác @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500">Địa chỉ</dt>
                            <dd class="mt-1 text-gray-800">{{ $employee->address ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Hàng Dưới: Thông tin công việc và lịch sử chấm công --}}
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-4">Thông tin công việc
                    </h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Chức vụ</dt>
                            <dd class="mt-1 text-gray-800">{{ $employee->position->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Phòng ban</dt>
                            <dd class="mt-1 text-gray-800">{{ $employee->department->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Ngày vào làm</dt>
                            <dd class="mt-1 text-gray-800">
                                {{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Ngày nghỉ việc</dt>
                            <dd class="mt-1 text-gray-800">
                                {{ $employee->termination_date ? \Carbon\Carbon::parse($employee->termination_date)->format('d/m/Y') : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Chấm công gần đây</h4>
                        <a href="{{ route('admin.attendances.index', ['employee_id' => $employee->id]) }}"
                            class="text-xs text-blue-600 hover:underline">Xem tất cả</a>
                    </div>
                    @if($employee->attendances->isNotEmpty())
                        <ul class="space-y-2 text-sm text-gray-700">
                            @foreach($employee->attendances as $attendance)
                                <li class="flex justify-between items-center border-b border-gray-200 last:border-b-0 py-2">
                                    <span>{{ $attendance->date->format('d/m/Y') }}</span>
                                    <span>
                                        Vào:
                                        {{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '--:--' }}
                                        |
                                        Ra:
                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '--:--' }}
                                    </span>
                                    <span
                                        class="text-xs {{ $attendance->status == 'late' ? 'text-yellow-600 font-semibold' : ($attendance->status == 'absent' ? 'text-red-600 font-semibold' : '') }}">
                                        {{ $attendance->status == 'late' ? 'Đi trễ' : ($attendance->status == 'absent' ? 'Vắng' : '') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 italic">Chưa có dữ liệu chấm công.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
    </div>
</x-admin-layout>