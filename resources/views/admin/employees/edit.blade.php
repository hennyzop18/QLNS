<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sửa Thông Tin Nhân Viên') }}: {{ $employee->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Hiển thị lỗi validation --}}
                    <x-validation-errors class="mb-4" :errors="$errors" />

                    {{-- Form method phải là POST, dùng @method để spoof PUT/PATCH --}}
                    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT') {{-- Hoặc PATCH --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Cột 1 --}}
                            <div>
                                <!-- Mã Nhân Viên (Chỉ đọc) -->
                                <div>
                                    <x-input-label for="employee_code" value="{{ __('Mã Nhân Viên') }}" />
                                    <x-text-input id="employee_code" class="block mt-1 w-full bg-gray-100" type="text"
                                        name="employee_code" :value="$employee->employee_code" readonly disabled />
                                     {{-- Thêm input hidden để gửi mã NV nếu cần trong request, mặc dù route đã có ID --}}
                                     <input type="hidden" name="employee_code" value="{{ $employee->employee_code }}">
                                </div>

                                <!-- Họ -->
                                <div class="mt-4">
                                    <x-input-label for="first_name" value="{{ __('Họ') }}" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text"
                                        name="first_name" :value="old('first_name', $employee->first_name)" required autofocus />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                <!-- Tên -->
                                <div class="mt-4">
                                    <x-input-label for="last_name" value="{{ __('Tên') }}" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                                        :value="old('last_name', $employee->last_name)" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>

                                <!-- Email đăng nhập (Lấy từ User liên kết) -->
                                <div class="mt-4">
                                    <x-input-label for="email" value="{{ __('Email đăng nhập') }}" />
                                    {{-- Sử dụng nullsafe operator (?->) phòng trường hợp employee chưa có user --}}
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email', $employee->user?->email)" />
                                     <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                     <!-- <p class="text-sm text-gray-500 mt-1">Email dùng để đăng nhập hệ thống (nếu nhân viên có tài khoản).</p> -->
                                </div>

                                <!-- Giới tính -->
                                <div class="mt-4">
                                    <x-input-label for="gender" value="{{ __('Giới tính') }}" />
                                    <select name="gender" id="gender"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="">Chọn giới tính</option>
                                        {{-- Sử dụng old() trước --}}
                                        <option value="male" @selected(old('gender', $employee->gender) == 'male')>Nam</option>
                                        <option value="female" @selected(old('gender', $employee->gender) == 'female')>Nữ</option>
                                        <option value="other" @selected(old('gender', $employee->gender) == 'other')>Khác</option>
                                    </select>
                                     <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <!-- Ngày sinh -->
                                <div class="mt-4">
                                    <x-input-label for="dob" value="{{ __('Ngày sinh') }}" />
                                    <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob"
                                        :value="old('dob', $employee->dob)" />
                                     <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                                </div>

                            </div>

                            {{-- Cột 2 --}}
                            <div>
                                <!-- Số điện thoại -->
                                <div>
                                    <x-input-label for="phone_number" value="{{ __('Số điện thoại') }}" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel"
                                        name="phone_number" :value="old('phone_number', $employee->phone_number)" />
                                     <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>

                                <!-- Địa chỉ -->
                                <div class="mt-4">
                                    <x-input-label for="address" value="{{ __('Địa chỉ') }}" />
                                    <textarea id="address" name="address" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $employee->address) }}</textarea>
                                     <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>


                                <!-- Ngày vào làm -->
                                <div class="mt-4">
                                    <x-input-label for="hire_date" value="{{ __('Ngày vào làm') }}" />
                                    <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date"
                                        :value="old('hire_date', $employee->hire_date)" required />
                                    <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                                </div>

                                <!-- Ngày nghỉ việc -->
                                <div class="mt-4">
                                    <x-input-label for="termination_date" value="{{ __('Ngày nghỉ việc (Để trống nếu đang làm)') }}" />
                                    <x-text-input id="termination_date" class="block mt-1 w-full" type="date" name="termination_date"
                                        :value="old('termination_date', $employee->termination_date)" />
                                     <x-input-error :messages="$errors->get('termination_date')" class="mt-2" />
                                </div>


                                <!-- Chức vụ -->
                                <div class="mt-4">
                                    <x-input-label for="position_id" value="{{ __('Chức vụ') }}" />
                                    <select name="position_id" id="position_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required>
                                        <option value="">Chọn chức vụ</option>
                                        @foreach($positions as $position) {{-- Đảm bảo $positions được truyền từ controller --}}
                                            <option value="{{ $position->id }}"
                                                @selected(old('position_id', $employee->position_id) == $position->id)>{{ $position->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                     <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
                                </div>

                                <!-- Phòng Ban -->
                                <div class="mt-4">
                                    <x-input-label for="department_id" value="{{ __('Phòng ban') }}" />
                                    <select name="department_id" id="department_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- Chọn phòng ban --</option>
                                        @foreach($departments as $department) {{-- Đảm bảo $departments được truyền từ controller --}}
                                            <option value="{{ $department->id }}"
                                                @selected(old('department_id', $employee->department_id) == $department->id)>{{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                     <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                 <!-- Trạng thái Nhân viên -->
                                 <div class="mt-4">
                                     <x-input-label for="status" value="{{ __('Trạng thái') }}" />
                                     <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                         <option value="active" @selected(old('status', $employee->status) == 'active')>Hoạt động</option>
                                         <option value="inactive" @selected(old('status', $employee->status) == 'inactive')>Tạm nghỉ</option>
                                         <option value="terminated" @selected(old('status', $employee->status) == 'terminated')>Đã nghỉ việc</option>
                                     </select>
                                      <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                 </div>

                                {{-- Bỏ phần Checkbox Tạo tài khoản --}}

                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.employees.index') }}">
                                <x-secondary-button type="button" class="mr-4">
                                    {{ __('Hủy bỏ') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button>
                                {{ __('Lưu Thay Đổi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>