<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    // GET /api/grades
    public function index()
    {
        // Load the grade, the student's profile, and the assignment details
        $grades = Grade::with(['student.user', 'assignment'])->get();
        return response()->json($grades);
    }

    // POST /api/grades
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'assignment_id' => 'required|exists:assignments,id',
            'score' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        // Optional: Ensure a student doesn't get graded twice for the same assignment
        $existingGrade = Grade::where('student_id', $validated['student_id'])
            ->where('assignment_id', $validated['assignment_id'])
            ->first();

        if ($existingGrade) {
            return response()->json(['message' => 'Grade already exists for this assignment'], 422);
        }

        $grade = Grade::create($validated);
        return response()->json($grade, 201);
    }

    // GET /api/grades/{id}
    public function show($id)
    {
        $grade = Grade::with(['student.user', 'assignment.section.subject'])->findOrFail($id);
        return response()->json($grade);
    }

    // PUT/PATCH /api/grades/{id}
    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'assignment_id' => 'sometimes|required|exists:assignments,id',
            'score' => 'sometimes|required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $grade->update($validated);
        return response()->json($grade);
    }

    // DELETE /api/grades/{id}
    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();
        return response()->json(['message' => 'Grade deleted successfully']);
    }
}