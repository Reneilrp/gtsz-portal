<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // GET /api/sections
    public function index()
    {
        // Fetch all sections with their school year, subject details, and the teacher's profile
        $sections = Section::with(['schoolYear', 'subject', 'teacher.user'])->get();

        return response()->json($sections);
    }

    // POST /api/sections
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string',
            'room' => 'nullable|string',
            'schedule' => 'nullable|string',
        ]);

        $section = Section::create($validated);

        return response()->json($section, 201);
    }

    // GET /api/sections/{id}
    public function show($id)
    {
        // This is a heavy query: loads the section, subject, teacher, AND every enrolled student
        $section = Section::with([
            'schoolYear',
            'subject',
            'teacher.user',
            'students.user' // Gets the student profiles linked to this specific section
        ])->findOrFail($id);

        return response()->json($section);
    }

    // PUT/PATCH /api/sections/{id}
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $validated = $request->validate([
            'school_year_id' => 'sometimes|required|exists:school_years,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'teacher_id' => 'sometimes|required|exists:teachers,id',
            'name' => 'sometimes|required|string',
            'room' => 'nullable|string',
            'schedule' => 'nullable|string',
        ]);

        $section->update($validated);

        return response()->json($section);
    }

    // DELETE /api/sections/{id}
    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json(['message' => 'Section deleted successfully']);
    }
}