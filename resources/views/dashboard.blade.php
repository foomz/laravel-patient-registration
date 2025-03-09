@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Patients</h5>
                                    <h2>{{ $totalPatients }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-success text-white">
                                <div class="card-body">
                                    <h5>Your Patients</h5>
                                    <h2>{{ $yourPatients }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-info text-white">
                                <div class="card-body">
                                    <h5>Total Comments</h5>
                                    <h2>{{ $totalComments }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-warning text-white">
                                <div class="card-body">
                                    <h5>Your Comments</h5>
                                    <h2>{{ $yourComments }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">Recent Patients</div>
                                <div class="card-body">
                                    @if($recentPatients->count() > 0)
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentPatients as $patient)
                                                    <tr>
                                                        <td>{{ $patient->name }}</td>
                                                        <td>{{ $patient->email }}</td>
                                                        <td>{{ $patient->phone }}</td>
                                                        <td>{{ $patient->created_at->diffForHumans() }}</td>
                                                        <td>
                                                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-center">No patients registered yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
