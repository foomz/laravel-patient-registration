<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $patients = Patient::paginate(10);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        
        Patient::create($validated);
        
        return redirect()->route('patients.index')
            ->with('success', 'Patient record created successfully');
    }

    public function show(Patient $patient)
    {
        $comments = $patient->comments()->with('user')->get();
        return view('patients.show', compact('patient', 'comments'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);
        
        $patient->update($validated);
        
        return redirect()->route('patients.index')
            ->with('success', 'Patient record updated successfully');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        
        return redirect()->route('patients.index')
            ->with('success', 'Patient record deleted successfully');
    }
}
