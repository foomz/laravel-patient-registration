@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Patient Records') }}</span>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">Add New Patient</a>
                </div>

                <div class="card-body">
                    @if($patients->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->id }}</td>
                                        <td>{{ $patient->name }}</td>
                                        <td>{{ $patient->email }}</td>
                                        <td>{{ $patient->phone }}</td>
                                        <td>{{ ucfirst($patient->gender) }}</td>
                                        <td>{{ $patient->date_of_birth }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this patient record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $patients->links() }}
                        </div>
                    @else
                        <p class="text-center">No patient records found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
