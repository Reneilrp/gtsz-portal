<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    // GET /api/school-years
    public function index()
    {
        $schoolYears = SchoolYear::paginate(10);
        return response()->json($schoolYears);
    }

    // POST /api/school-years
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $schoolYear = SchoolYear::create($validated);
        return response()->json($schoolYear, 201);
    }

    // GET /api/school-years/{id}
    public function show($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);
        return response()->json($schoolYear);
    }

    // PUT/PATCH /api/school-years/{id}
    public function update(Request $request, $id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $schoolYear->update($validated);
        return response()->json($schoolYear);
    }

    // DELETE /api/school-years/{id}
    public function destroy($id)
    {
        $schoolYear = SchoolYear::findOrFail($id);
        $schoolYear->delete();
        return response()->json(['message' => 'School Year deleted successfully']);
    }
}
