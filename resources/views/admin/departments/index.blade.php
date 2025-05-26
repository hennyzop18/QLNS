<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Phòng Ban') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="mb-4 flex justify-between items-center">
                        <a href="{{ route('admin.departments.create') }}">
                            <x-primary-button>
                                {{ __('Thêm Phòng Ban') }}
                            </x-primary-button>
                        </a>
                        {{-- TODO: Thêm form tìm kiếm nếu cần --}}
                    </div>

                    {{-- Hiển thị thông báo thành công/lỗi --}}
                    <x-session-status class="mb-4" :status="session('success')" />
                    @if(session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif


                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tên Phòng Ban</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mô tả</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số lượng NV</th>
                                    <th scope="col" class="relative px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($departments as $department)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $department->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                                            {{ Str::limit($department->description, 100) }}
                                        </td> {{-- Giới hạn độ dài mô tả
                                        --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $department->employees_count ?? $department->employees()->count() }}
                                        </td>
                                        {{-- Hiển thị số lượng NV (nếu đã load count) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            {{-- Thêm space-x-2 --}}
                                            {{-- Link đến trang show (nếu có) --}}
                                            {{-- <a href="{{ route('admin.departments.show', $department) }}"
                                                class="text-indigo-600 hover:text-indigo-900">Xem</a> --}}

                                            <a href="{{ route('admin.departments.edit', $department) }}"
                                                class="text-blue-600 hover:text-blue-900">Sửa</a>

                                            {{-- Form Xóa (Sử dụng Soft Delete nếu đã cấu hình) --}}
                                            <form action="{{ route('admin.departments.destroy', $department) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Bạn chắc chắn muốn ẩn phòng ban này?');"> {{--
                                                Thay đổi confirm message nếu dùng soft delete --}}
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit" class="text-xs">
                                                    {{-- {{ $department->trashed() ? 'Xóa VV' : 'Ẩn' }} --}} {{-- Text tùy
                                                    theo trạng thái nếu có thùng rác --}}
                                                    Ẩn {{-- Hoặc Xóa --}}
                                                </x-danger-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có phòng
                                            ban nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-4">
                        {{ $departments->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>