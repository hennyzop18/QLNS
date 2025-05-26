<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Tài khoản Người Dùng') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8"> {{-- max-w-full cho bảng rộng --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Form Lọc --}}
                    <form method="GET" action="{{ route('admin.users.index') }}"
                        class="mb-6 p-4 bg-gray-50 rounded-md border">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <x-input-label for="search" value="Tên / Email" />
                                <x-text-input type="text" name="search" id="search" class="block w-full mt-1"
                                    :value="$search ?? ''" placeholder="Nhập tên hoặc email..." />
                            </div>
                            <div>
                                <x-input-label for="role" value="Vai trò" />
                                <select name="role" id="role"
                                    class="block w-full mt-1 border-gray-300 ... rounded-md shadow-sm">
                                    <option value="">-- Tất cả vai trò --</option>
                                    <option value="admin" @selected($role == 'admin')>Admin</option>
                                    <option value="employee" @selected($role == 'employee')>Employee</option>
                                </select>
                            </div>
                            {{-- Có thể thêm lọc theo trạng thái is_active --}}
                            {{-- <div>
                                <x-input-label for="is_active" value="Trạng thái TK" />
                                <select name="is_active" id="is_active" class="block w-full mt-1 ...">
                                    <option value="">-- Tất cả --</option>
                                    <option value="1" @selected(request('is_active')=='1' )>Hoạt động</option>
                                    <option value="0" @selected(request('is_active')==='0' )>Bị khóa</option>
                                </select>
                            </div> --}}
                            <div class="flex space-x-2">
                                <x-primary-button type="submit">Lọc</x-primary-button>
                                <a href="{{ route('admin.users.index') }}">
                                    <x-secondary-button type="button">Xóa lọc</x-secondary-button>
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('admin.users.create') }}">
                            <x-primary-button>
                                {{ __('Thêm Tài Khoản Mới') }}
                            </x-primary-button>
                        </a>
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
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tên Người Dùng</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vai trò</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nhân viên liên kết</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trạng thái TK</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ngày tạo</th>
                                    <th class="relative px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)

                                                                <tr>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        {{ $user->name }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                        <span
                                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role == 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                                                            {{ ucfirst($user->role) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        @if($user->employee)
                                                                            {{ $user->employee->full_name }} ({{$user->employee->employee_code}})
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                                        {{-- Status Badge cho is_active --}}
                                                                        @php
                                                                            $activeClass = $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                                                            $activeText = $user->is_active ? 'Hoạt động' : 'Bị khóa';
                                                                        @endphp
                                                                        <span
                                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $activeClass }}">
                                                                            {{ $activeText }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                                        {{-- <a href="{{ route('admin.users.show', $user->id) }}"
                                                                            class="text-indigo-600 hover:text-indigo-900">Xem</a> --}}
                                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                                            class="text-blue-600 hover:text-blue-900">Sửa</a>
                                                                        {{-- Không nên cho xóa user đang đăng nhập và admin cuối cùng --}}
                                                                        @if(Auth::id() !== $user->id && !($user->isAdmin() && \App\Models\User::where('role', 'admin')->count() <= 1))
                                                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                                                class="inline-block"
                                                                                onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản này?');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <x-danger-button type="submit" class="text-xs">Xóa</x-danger-button>
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Không tìm thấy
                                            tài khoản nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>