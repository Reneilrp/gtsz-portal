<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // GET /api/teachers
    public function index()
    {
        // Fetch teachers and include their linked User profile data (name, email)
        $teachers = Teacher::with('user')->get();

        return response()->json($teachers);
    }

    // POST /api/teachers
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'employee_number' => 'required|string|unique:teachers',
            'department' => 'required|string',
            'specialization' => 'nullable|string',
            'hired_date' => 'required|date',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json($teacher, 201);
    }

    // GET /api/teachers/{id}
    public function show($id)
    {
        // Load the teacher's profile, and all the sections/classes they are currently teaching
        $teacher = Teacher::with(['user', 'sections.subject', 'sections.schoolYear'])->findOrFail($id);

        return response()->json($teacher);
    }

    // PUT/PATCH /api/teachers/{id}
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        // In a real app, you'd add validation here before updating
        $teacher->update($request->all());

        return response()->json($teacher);
    }

    // DELETE /api/teachers/{id}
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Teacher deleted successfully']);
    }
}