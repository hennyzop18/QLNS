<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo Bảng Lương Mới') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-validation-errors class="mb-4" :errors="$errors" />
                    @if(session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p class="mb-4 text-sm text-gray-600">Chọn tháng và năm để tạo (hoặc cập nhật nếu đã tồn tại) bảng
                        lương cho tất cả nhân viên đủ điều kiện.</p>

                    <form method="POST" action="{{ route('admin.salaries.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="month" value="{{ __('Tháng') }}" />
                                <select name="month" id="month"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected(old('month', now()->month) == $m)>
                                            Tháng {{ $m }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="year" value="{{ __('Năm') }}" />
                                <x-text-input type="number" name="year" id="year" class="block mt-1 w-full" min="2020"
                                    max="{{ now()->year + 1 }}" :value="old('year', now()->year)" required />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Tạo/Cập Nhật Bảng Lương') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>