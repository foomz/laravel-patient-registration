<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->patient_id = $patient->id;
        $comment->user_id = Auth::id();
        $comment->save();

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'Comment added successfully');
    }

    public function destroy(Comment $comment)
    {
        $patientId = $comment->patient_id;
        
        // Check if the user is authorized to delete this comment
        if (Auth::id() !== $comment->user_id) {
            return redirect()->route('patients.show', $patientId)
                ->with('error', 'You are not authorized to delete this comment');
        }
        
        $comment->delete();
        
        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Comment deleted successfully');
    }
}
