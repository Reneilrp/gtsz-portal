<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    // GET /api/assignments
    public function index()
    {
        // Load the assignment along with the section and subject it belongs to
        $assignments = Assignment::with('section.subject')->get();
        return response()->json($assignments);
    }

    // POST /api/assignments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_score' => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]);

        $assignment = Assignment::create($validated);
        return response()->json($assignment, 201);
    }

    // GET /api/assignments/{id}
    public function show($id)
    {
        // Load the assignment, its section, and all the grades submitted for it so far
        $assignment = Assignment::with(['section.subject', 'section.teacher.user'])->findOrFail($id);
        return response()->json($assignment);
    }

    // PUT/PATCH /api/assignments/{id}
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->all());
        return response()->json($assignment);
    }

    // DELETE /api/assignments/{id}
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}