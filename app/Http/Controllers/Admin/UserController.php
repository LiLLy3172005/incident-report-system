<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('reports');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            } elseif ($request->status === 'warned') {
                $query->where('strikes', '>', 0)->where('is_banned', false);
            }
        }

        $users = $query->where('role', 'user')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function ban(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => true]);
        return back()->with('success', "Đã khóa tài khoản {$user->name}");
    }

    public function unban(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_banned' => false, 'strikes' => 0]);
        return back()->with('success', "Đã mở khóa tài khoản {$user->name}");
    }

    public function resetStrikes(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['strikes' => 0]);
        return back()->with('success', "Đã reset strikes cho {$user->name}");
    }
}