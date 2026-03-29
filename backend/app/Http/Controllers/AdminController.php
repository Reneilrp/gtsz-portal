<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Get all users with roles.
     */
    public function getUsers()
    {
        return response()->json(User::with('role')->latest()->get());
    }

    /**
     * Create a new Admin user (Super Admin only).
     */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        $user = User::create([
            'role_id' => $adminRole->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        return response()->json([
            'user' => $user->load('role'),
            'message' => 'Admin account created successfully.'
        ], 201);
    }

    /**
     * Get system-wide stats.
     */
    public function getStats()
    {
        $roles = Role::withCount('users')->get();
        
        $stats = [
            'totalUsers' => User::count(),
            'admins' => $roles->where('name', 'Admin')->first()->users_count ?? 0,
            'teachers' => $roles->where('name', 'Teacher')->first()->users_count ?? 0,
            'students' => $roles->where('name', 'Student')->first()->users_count ?? 0,
        ];

        return response()->json($stats);
    }
}
