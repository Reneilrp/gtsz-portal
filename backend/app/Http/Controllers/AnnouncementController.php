<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    // GET /api/announcements
    public function index()
    {
        $announcements = Announcement::with(['author', 'targetRole'])->get();
        return response()->json($announcements);
    }

    // POST /api/announcements
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role_id' => 'nullable|exists:roles,id',
        ]);

        $announcement = Announcement::create($validated);
        return response()->json($announcement, 201);
    }

    // GET /api/announcements/{id}
    public function show($id)
    {
        $announcement = Announcement::with(['author', 'targetRole'])->findOrFail($id);
        return response()->json($announcement);
    }

    // PUT/PATCH /api/announcements/{id}
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'target_role_id' => 'nullable|exists:roles,id',
        ]);

        $announcement->update($validated);
        return response()->json($announcement);
    }

    // DELETE /api/announcements/{id}
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted successfully']);
    }
}
