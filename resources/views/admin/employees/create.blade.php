<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thêm Nhân Viên Mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Hiển thị lỗi validation --}}
                    <x-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('admin.employees.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Cột 1 --}}
                            <div>
                                <!-- Mã Nhân Viên -->
                                <div>
                                    <x-input-label for="employee_code" value="{{ __('Mã Nhân Viên') }}" />
                                    <x-text-input id="employee_code" class="block mt-1 w-full" type="text"
                                        name="employee_code" :value="old('employee_code')" required autofocus />
                                    <x-input-error :messages="$errors->get('employee_code')" class="mt-2" />
                                </div>

                                <!-- Họ -->
                                <div class="mt-4">
                                    <x-input-label for="first_name" value="{{ __('Họ') }}" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text"
                                        name="first_name" :value="old('first_name')" required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                <!-- Tên -->
                                <div class="mt-4">
                                    <x-input-label for="last_name" value="{{ __('Tên') }}" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                                        :value="old('last_name')" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>

                                <!-- Email (cho tài khoản user) -->
                                <div class="mt-4">
                                    <x-input-label for="email" value="{{ __('Email đăng nhập') }}" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email')" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    <p class="text-sm text-gray-600 mt-1">Bắt buộc nếu chọn "Tạo tài khoản".</p>
                                </div>

                                <!-- Giới tính -->
                                <div class="mt-4">
                                    <x-input-label for="gender" value="{{ __('Giới tính') }}" />
                                    <select name="gender" id="gender"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        required>
                                        <option value="">Chọn giới tính</option>
                                        <option value="male" @selected(old('gender') == 'male')>Nam</option>
                                        <option value="female" @selected(old('gender') == 'female')>Nữ</option>
                                        <option value="other" @selected(old('gender') == 'other')>Khác</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                </div>

                                <!-- Ngày sinh -->
                                <div class="mt-4">
                                    <x-input-label for="dob" value="{{ __('Ngày sinh') }}" />
                                    <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob"
                                        :value="old('dob')" />
                                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                                </div>

                            </div>

                            {{-- Cột 2 --}}
                            <div>
                                <!-- Số điện thoại -->
                                <div>
                                    <x-input-label for="phone_number" value="{{ __('Số điện thoại') }}" />
                                    <x-text-input id="phone_number" class="block mt-1 w-full" type="tel"
                                        name="phone_number" :value="old('phone_number')" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                                </div>

                                <!-- Địa chỉ -->
                                <div class="mt-4">
                                    <x-input-label for="address" value="{{ __('Địa chỉ') }}" />
                                    <textarea id="address" name="address" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>


                                <!-- Ngày vào làm -->
                                <div class="mt-4">
                                    <x-input-label for="hire_date" value="{{ __('Ngày vào làm') }}" />
                                    <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date"
                                        :value="old('hire_date')" required />
                                    <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                                </div>

                                <!-- Chức vụ -->
                                <div class="mt-4">
                                    <x-input-label for="position_id" value="{{ __('Chức vụ') }}" />
                                    <select name="position_id" id="position_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        required>
                                        <option value="">Chọn chức vụ</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}"
                                                @selected(old('position_id') == $position->id)>{{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
                                </div>

                                <!-- Phòng Ban (nếu có) -->
                                <div class="mt-4">
                                    <x-input-label for="department_id" value="{{ __('Phòng ban') }}" />
                                    <select name="department_id" id="department_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                        <option value="">-- Chọn phòng ban --</option>
                                        @foreach($departments as $department) {{-- Giả sử $departments được truyền từ
                                            controller --}}
                                            <option value="{{ $department->id }}"
                                                @selected(old('department_id') == $department->id)>{{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <!-- Tạo tài khoản -->
                                <div class="block mt-4">
                                    <label for="create_account" class="inline-flex items-center">
                                        {{-- Dùng component checkbox nếu có hoặc HTML chuẩn --}}
                                        <input id="create_account" type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            name="create_account" value="1" @checked(old('create_account', true))>
                                        <span
                                            class="ml-2 text-sm text-gray-600">{{ __('Tự động tạo tài khoản đăng nhập') }}</span>
                                    </label>
                                </div>

                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.employees.index') }}">
                                {{-- Sử dụng secondary button hoặc link thường --}}
                                <x-secondary-button type="button" class="mr-4">
                                    {{ __('Hủy bỏ') }}
                                </x-secondary-button>
                            </a>

                            <x-primary-button>
                                {{ __('Lưu Nhân Viên') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>