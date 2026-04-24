<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // GET /api/attendances
    public function index()
    {
        $attendances = Attendance::with(['student.user', 'section.subject'])->get();
        return response()->json($attendances);
    }

    // POST /api/attendances
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'remarks' => 'nullable|string',
        ]);

        $existingAttendance = Attendance::where('student_id', $validated['student_id'])
            ->where('section_id', $validated['section_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'Attendance already exists for this date and section'], 422);
        }

        $attendance = Attendance::create($validated);
        return response()->json($attendance, 201);
    }

    // GET /api/attendances/{id}
    public function show($id)
    {
        $attendance = Attendance::with(['student.user', 'section.subject'])->findOrFail($id);
        return response()->json($attendance);
    }

    // PUT/PATCH /api/attendances/{id}
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:present,absent,late,excused',
            'remarks' => 'nullable|string',
        ]);

        $attendance->update($validated);
        return response()->json($attendance);
    }

    // DELETE /api/attendances/{id}
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        return response()->json(['message' => 'Attendance deleted successfully']);
    }
}
