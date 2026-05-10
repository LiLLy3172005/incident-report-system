@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">👥 Quản lý người dùng</h1>

    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tên, số điện thoại..." class="border rounded-lg px-3 py-2 text-sm flex-1">
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="warned" {{ request('status') === 'warned' ? 'selected' : '' }}>Bị cảnh cáo</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã khóa</option>
            </select>
            <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm">Lọc</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Tên</th>
                    <th class="p-3 text-left">SĐT</th>
                    <th class="p-3 text-center">Báo cáo</th>
                    <th class="p-3 text-center">Strikes</th>
                    <th class="p-3 text-center">Trạng thái</th>
                    <th class="p-3 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b hover:bg-gray-50 {{ $user->is_banned ? 'bg-red-50' : '' }}">
                    <td class="p-3">#{{ $user->id }}</td>
                    <td class="p-3 font-medium">{{ $user->name }}</td>
                    <td class="p-3 text-gray-500">{{ $user->phone }}</td>
                    <td class="p-3 text-center">{{ $user->reports_count }}</td>
                    <td class="p-3 text-center">
                        <span class="font-bold {{ $user->strikes >= 3 ? 'text-red-600' : ($user->strikes > 0 ? 'text-yellow-600' : 'text-gray-400') }}">
                            {{ $user->strikes }}/3
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        @if($user->is_banned)
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">BANNED</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex justify-center gap-2">
                            @if($user->is_banned)
                                <form action="{{ route('admin.users.unban', $user->id) }}" method="POST">
                                    @csrf
                                    <button class="text-green-600 text-xs hover:underline">Mở khóa</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                    @csrf
                                    <button class="text-red-600 text-xs hover:underline" onclick="return confirm('Khóa user này?')">Khóa</button>
                                </form>
                            @endif
                            @if($user->strikes > 0)
                                <form action="{{ route('admin.users.reset-strikes', $user->id) }}" method="POST">
                                    @csrf
                                    <button class="text-blue-600 text-xs hover:underline">Reset</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection