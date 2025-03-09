<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalPatients = Patient::count();
        $yourPatients = Patient::where('user_id', Auth::id())->count();
        $totalComments = Comment::count();
        $yourComments = Comment::where('user_id', Auth::id())->count();
        $recentPatients = Patient::latest()->take(5)->get();
        
        return view('dashboard', compact(
            'totalPatients',
            'yourPatients',
            'totalComments',
            'yourComments',
            'recentPatients'
        ));
    }
}
