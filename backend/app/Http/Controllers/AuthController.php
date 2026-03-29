<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user (Student or Teacher).
     */
    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required|in:Student,Teacher',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // Student fields
            'student_number' => 'required_if:role,Student|nullable|string|unique:students',
            'birth_date' => 'required_if:role,Student|nullable|date',
            'gender' => 'required_if:role,Student|nullable|string',
            'address' => 'required_if:role,Student|nullable|string',
            'guardian_name' => 'required_if:role,Student|nullable|string',
            'guardian_contact' => 'required_if:role,Student|nullable|string',
            'school_year_id' => 'required_if:role,Student|nullable|exists:school_years,id',
            // Teacher fields
            'employee_number' => 'required_if:role,Teacher|nullable|string|unique:teachers',
            'department' => 'required_if:role,Teacher|nullable|string',
            'hired_date' => 'required_if:role,Teacher|nullable|date',
        ]);

        return DB::transaction(function () use ($request) {
            $role = Role::where('name', $request->role)->firstOrFail();

            $user = User::create([
                'role_id' => $role->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            if ($request->role === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'student_number' => $request->student_number,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'guardian_name' => $request->guardian_name,
                    'guardian_contact' => $request->guardian_contact,
                    'school_year_id' => $request->school_year_id,
                ]);
            } else if ($request->role === 'Teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                    'employee_number' => $request->employee_number,
                    'department' => $request->department,
                    'hired_date' => $request->hired_date,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user->load('role'),
                'token' => $token,
                'message' => 'Registration successful.',
            ], 201);
        });
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Your account is disabled.'], 403);
        }

        $user->update(['last_login' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('role'),
            'token' => $token,
        ]);
    }

    /**
     * Logout user (Revoke token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load(['role', 'student', 'teacher'])
        ]);
    }
}
