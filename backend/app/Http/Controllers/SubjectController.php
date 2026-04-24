<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // GET /api/subjects
    public function index()
    {
        $subjects = Subject::all();
        return response()->json($subjects);
    }

    // POST /api/subjects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects',
            'description' => 'nullable|string',
        ]);

        $subject = Subject::create($validated);
        return response()->json($subject, 201);
    }

    // GET /api/subjects/{id}
    public function show($id)
    {
        $subject = Subject::with('sections')->findOrFail($id);
        return response()->json($subject);
    }

    // PUT/PATCH /api/subjects/{id}
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:255|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);
        return response()->json($subject);
    }

    // DELETE /api/subjects/{id}
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
