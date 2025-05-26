<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Chi Tiết Lương') }} - {{ $salary->employee->full_name ?? 'N/A' }} - Kỳ
                {{ $salary->pay_period_start->format('m/Y') }}
            </h2>
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('admin.salaries.index', ['month' => $salary->pay_period_start->month, 'year' => $salary->pay_period_start->year]) }}">
                    <x-secondary-button>
                        ← {{ __('Danh sách lương kỳ') }} {{ $salary->pay_period_start->format('m/Y') }}
                    </x-secondary-button>
                </a>
                @if($salary->status == 'pending')
                    <a href="{{ route('admin.salaries.edit', $salary) }}">
                        <x-primary-button>
                            {{ __('Sửa Chi Tiết') }}
                        </x-primary-button>
                    </a>
                    {{-- Form Đánh dấu đã thanh toán --}}
                    <form action="{{ route('admin.salaries.update', $salary) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Xác nhận đã thanh toán lương?');">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="mark_as_paid" value="1">
                        <x-primary-button type="submit"
                            class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500">
                            Thanh Toán
                        </x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 bg-white border-b border-gray-200 space-y-6">

                    {{-- Thông Tin Chung --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b pb-4">
                        <div>
                            <span class="font-semibold text-gray-600">Nhân viên:</span>
                            <p class="text-gray-900 font-medium">{{ $salary->employee->full_name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">({{ $salary->employee->employee_code ?? 'N/A' }})</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-600">Phòng ban:</span>
                            <p class="text-gray-900">{{ $salary->employee->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-600">Kỳ lương:</span>
                            <p class="text-gray-900">{{ $salary->pay_period_start->format('d/m/Y') }} -
                                {{ $salary->pay_period_end->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Chi Tiết Lương --}}
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Chi tiết tính lương</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="sm:col-span-1">
                                <dt class="font-medium text-gray-500">Lương cơ bản</dt>
                                <dd class="mt-1 text-gray-900 text-right">
                                    {{ number_format($salary->base_salary, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="font-medium text-gray-500">Phụ cấp (+)</dt>
                                <dd class="mt-1 text-gray-900 text-right">
                                    {{ number_format($salary->allowances, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="font-medium text-gray-500">Khấu trừ (-)</dt>
                                <dd class="mt-1 text-gray-900 text-right text-red-600">
                                    {{ number_format($salary->deductions, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="font-medium text-gray-500">Thưởng (Khen thưởng) (+)</dt>
                                <dd class="mt-1 text-gray-900 text-right">
                                    {{ number_format($salary->bonus, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="font-medium text-gray-500">Phạt (Kỷ luật) (-)</dt>
                                <dd class="mt-1 text-gray-900 text-right text-red-600">
                                    {{ number_format($salary->fines, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            <div class="sm:col-span-1 border-t pt-2 mt-2">
                                <dt class="font-semibold text-gray-700 text-base">Lương Thực Nhận (=)</dt>
                                <dd class="mt-1 text-indigo-700 text-right font-bold text-lg">
                                    {{ number_format($salary->net_salary, 0, ',', '.') }} đ
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Trạng thái thanh toán --}}
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Trạng thái & Ghi chú</h4>
                        <div class="flex items-center space-x-4 mb-2">
                            <span class="font-semibold text-gray-600">Trạng thái:</span>
                            {{-- Status Badge --}}
                            @php
                                $statusClass = match ($salary->status) {
                                    'paid' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $statusText = match ($salary->status) {
                                    'paid' => 'Đã TT',
                                    'pending' => 'Chờ TT',
                                    'cancelled' => 'Đã hủy',
                                    default => ucfirst($salary->status),
                                };
                            @endphp
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        @if($salary->paid_date)
                            <div class="text-sm">
                                <span class="font-semibold text-gray-600">Ngày thanh toán:</span>
                                <span class="text-gray-800">{{ $salary->paid_date->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        <div class="mt-2">
                            <span class="font-semibold text-gray-600">Ghi chú:</span>
                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap">{{ $salary->notes ?: 'Không có' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>