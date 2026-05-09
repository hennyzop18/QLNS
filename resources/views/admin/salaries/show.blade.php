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
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-700">Chi tiết tính lương</h4>
                            {{-- Badge chế độ lương --}}
                            @php
                                $typeLabels = ['fixed' => '💼 Lương cố định', 'hourly' => '⏱ Lương theo giờ', 'override' => '✏️ Ghi đè đồng loạt'];
                                $typeColors = ['fixed' => 'bg-blue-100 text-blue-800', 'hourly' => 'bg-purple-100 text-purple-800', 'override' => 'bg-amber-100 text-amber-800'];
                                $sType = $salary->salary_type ?? 'fixed';
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$sType] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $typeLabels[$sType] ?? $sType }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">

                            {{-- ─── PHẦN GROSS ─── --}}
                            @if($sType === 'hourly')
                            <div class="sm:col-span-2 bg-purple-50 rounded p-3">
                                <dt class="font-medium text-purple-700">Tổng giờ làm / OT</dt>
                                <dd class="mt-1 text-purple-900 font-semibold">
                                    {{ number_format($salary->total_actual_hours ?? 0, 2) }} giờ (thường) + {{ number_format($salary->ot_hours ?? 0, 2) }} giờ (OT)
                                    × {{ number_format($salary->hourly_rate_snapshot ?? 0, 0, ',', '.') }} đ/giờ
                                    = {{ number_format((($salary->total_actual_hours ?? 0) + ($salary->ot_hours ?? 0) * 1.5) * ($salary->hourly_rate_snapshot ?? 0), 0, ',', '.') }} đ
                                </dd>
                            </div>
                            @elseif($sType === 'override')
                            <div class="sm:col-span-2 bg-amber-50 rounded p-3">
                                <dt class="font-medium text-amber-700">Lương ghi đè (admin nhập)</dt>
                                <dd class="mt-1 text-amber-900 font-semibold">
                                    {{ number_format($salary->default_salary_override ?? 0, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            @else
                            <div>
                                <dt class="font-medium text-gray-500">Lương cơ bản (gross)</dt>
                                <dd class="mt-1 text-gray-900 text-right">{{ number_format($salary->base_salary, 0, ',', '.') }} đ</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Ngày công thực tế / chuẩn</dt>
                                <dd class="mt-1 text-gray-900 text-right">
                                    {{ $salary->actual_work_days ?? 0 }} / {{ $salary->standard_work_days ?? 22 }} ngày
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Lương tính theo công (prorated + OT)</dt>
                                <dd class="mt-1 text-gray-900 text-right font-medium">{{ number_format($salary->prorated_salary ?? 0, 0, ',', '.') }} đ</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Ngày vắng / đi trễ</dt>
                                <dd class="mt-1 text-red-600 text-right">{{ $salary->absent_days ?? 0 }} ngày vắng, {{ $salary->late_days ?? 0 }} ngày trễ</dd>
                            </div>
                            @endif

                            {{-- ─── GROSS SAU PHỤ CẤP & THƯỞNG ─── --}}
                            <div class="sm:col-span-1 border-t pt-2">
                                <dt class="font-medium text-gray-500">Phụ cấp chịu thuế (+)</dt>
                                <dd class="mt-1 text-green-700 text-right">+ {{ number_format($salary->taxable_allowances ?? 0, 0, ',', '.') }} đ</dd>
                            </div>
                            <div class="sm:col-span-1 border-t pt-2">
                                <dt class="font-medium text-gray-500">Phụ cấp KHÔNG chịu thuế (+)</dt>
                                <dd class="mt-1 text-green-700 text-right">+ {{ number_format($salary->nontaxable_allowances ?? 0, 0, ',', '.') }} đ</dd>
                            </div>
                            <div class="sm:col-span-1 border-t pt-2">
                                <dt class="font-medium text-gray-500">Thưởng Khen thưởng (+)</dt>
                                <dd class="mt-1 text-green-700 text-right">+ {{ number_format($salary->bonus, 0, ',', '.') }} đ</dd>
                            </div>

                            {{-- ─── BHXH CHI TIẾT ─── --}}
                            <div class="sm:col-span-2 mt-2">
                                <dt class="font-medium text-gray-600 mb-1">Khấu trừ Bảo hiểm bắt buộc (10.5%)</dt>
                                <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 bg-gray-50 rounded p-2">
                                    <div>BHXH (8%) <span class="block font-semibold text-red-600">- {{ number_format($salary->si_deduction ?? 0, 0, ',', '.') }} đ</span></div>
                                    <div>BHYT (1.5%) <span class="block font-semibold text-red-600">- {{ number_format($salary->hi_deduction ?? 0, 0, ',', '.') }} đ</span></div>
                                    <div>BHTN (1%) <span class="block font-semibold text-red-600">- {{ number_format($salary->ui_deduction ?? 0, 0, ',', '.') }} đ</span></div>
                                </div>
                            </div>

                            {{-- ─── THUẾ TNCN ─── --}}
                            <div>
                                <dt class="font-medium text-gray-500">Thu nhập chịu thuế</dt>
                                <dd class="mt-1 text-gray-700 text-right">{{ number_format($salary->taxable_income ?? 0, 0, ',', '.') }} đ</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Thuế TNCN (7 bậc lũy tiến)</dt>
                                <dd class="mt-1 text-red-600 text-right">- {{ number_format($salary->pit_tax ?? 0, 0, ',', '.') }} đ</dd>
                            </div>

                            {{-- ─── PHẠT & KỶ LUẬT ─── --}}
                            <div>
                                <dt class="font-medium text-gray-500">Phạt kỷ luật (-)</dt>
                                <dd class="mt-1 text-red-600 text-right">- {{ number_format($salary->fines, 0, ',', '.') }} đ</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Phạt đi trễ (-)</dt>
                                <dd class="mt-1 text-red-600 text-right">- {{ number_format($salary->late_fine ?? 0, 0, ',', '.') }} đ</dd>
                            </div>

                            {{-- ─── NET ─── --}}
                            <div class="sm:col-span-2 border-t-2 border-indigo-200 pt-3 mt-2 bg-indigo-50 rounded p-3">
                                <dt class="font-bold text-indigo-700 text-base">💵 Lương Thực Nhận (NET)</dt>
                                <dd class="mt-1 text-indigo-800 text-right font-extrabold text-2xl">
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