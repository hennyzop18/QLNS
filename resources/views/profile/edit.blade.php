<x-app-layout> {{-- Hoặc x-employee-layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Thông tin cá nhân') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Phần cập nhật thông tin Employee --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Thông tin nhân viên') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Cập nhật thông tin cá nhân của bạn.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('employee.profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch') {{-- Hoặc PUT tùy route definition --}}

                            {{-- Thông tin không được sửa (chỉ hiển thị) --}}
                            <div class="mt-4">
                                <x-label for="employee_code" value="{{ __('Mã Nhân Viên') }}" />
                                <x-input id="employee_code" class="block mt-1 w-full bg-gray-100" type="text" :value="$employee->employee_code" disabled readonly />
                            </div>
                             <div class="mt-4">
                                 <x-label value="{{ __('Họ và tên') }}" />
                                 <x-input class="block mt-1 w-full bg-gray-100" type="text" :value="$employee->full_name" disabled readonly />
                             </div>
                             {{-- Thêm các trường khác không được sửa nếu cần: Chức vụ, Ngày vào làm... --}}


                            {{-- Thông tin được phép sửa --}}
                            <div class="mt-4">
                                <x-label for="phone_number" value="{{ __('Số điện thoại') }}" />
                                <x-input id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" :value="old('phone_number', $employee->phone_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                            </div>

                            <div class="mt-4">
                                 <x-label for="personal_email" value="{{ __('Email cá nhân') }}" />
                                 <x-input id="personal_email" name="personal_email" type="email" class="mt-1 block w-full" :value="old('personal_email', $employee->personal_email)" />
                                 <x-input-error class="mt-2" :messages="$errors->get('personal_email')" />
                            </div>


                            <div class="mt-4">
                                <x-label for="address" value="{{ __('Địa chỉ') }}" />
                                <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ old('address', $employee->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>


                             <hr class="my-6">

                            {{-- Phần cập nhật thông tin User --}}
                              <h3 class="text-lg font-medium text-gray-900">
                                  {{ __('Thông tin tài khoản') }}
                              </h3>
                              <p class="mt-1 text-sm text-gray-600">
                                  {{ __("Cập nhật tên hiển thị và email đăng nhập.") }}
                              </p>
                            <div>
                                <x-label for="name" :value="__('Tên hiển thị')" />
                                <x-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-label for="email" :value="__('Email đăng nhập')" />
                                <x-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    {{-- Logic hiển thị thông báo nếu email chưa verify --}}
                                @endif
                            </div>

                             <hr class="my-6">
                              {{-- Phần đổi mật khẩu --}}
                               <h3 class="text-lg font-medium text-gray-900">
                                   {{ __('Đổi mật khẩu') }}
                               </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Để trống nếu bạn không muốn đổi mật khẩu.") }}
                                </p>
                             <div class="mt-4">
                                 <x-label for="current_password" value="{{ __('Mật khẩu hiện tại') }}" />
                                 <x-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                 <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                             </div>

                             <div class="mt-4">
                                 <x-label for="password" value="{{ __('Mật khẩu mới') }}" />
                                 <x-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                 <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                             </div>

                             <div class="mt-4">
                                 <x-label for="password_confirmation" value="{{ __('Xác nhận mật khẩu mới') }}" />
                                 <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password"/>
                                 <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                             </div>


                            <div class="flex items-center gap-4 mt-6">
                                <x-button>{{ __('Lưu thay đổi') }}</x-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Đã lưu.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
            {{-- Có thể thêm các section khác nếu cần --}}
        </div>
    </div>
</x-app-layout>