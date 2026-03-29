<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /api/students
    public function index()
    {
        // Fetch all students with their related User profile and School Year
        $students = Student::with(['user', 'schoolYear'])->get();

        return response()->json($students);
    }

    // POST /api/students
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'student_number' => 'required|string|unique:students',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'guardian_name' => 'required|string',
            'guardian_contact' => 'required|string',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        $student = Student::create($validated);

        return response()->json($student, 201);
    }

    // GET /api/students/{id}
    public function show($id)
    {
        // Find a specific student and load their sections and grades too
        $student = Student::with(['user', 'schoolYear', 'sections.subject', 'grades'])->findOrFail($id);

        return response()->json($student);
    }

    // PUT/PATCH /api/students/{id}
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->all());

        return response()->json($student);
    }

    // DELETE /api/students/{id}
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}