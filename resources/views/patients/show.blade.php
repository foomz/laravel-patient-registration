@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Patient Details') }}</div>

                <div class="card-body">
                    <p><strong>Name:</strong> {{ $patient->name }}</p>
                    <p><strong>Email:</strong> {{ $patient->email }}</p>
                    <p><strong>Phone:</strong> {{ $patient->phone }}</p>
                    <p><strong>Date of Birth:</strong> {{ $patient->date_of_birth }}</p>
                    <p><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</p>
                    <p><strong>Address:</strong> {{ $patient->address }}</p>
                    <p><strong>Medical History:</strong> {{ $patient->medical_history }}</p>

                    <hr>

                    <h5>Comments</h5>
                    @foreach($comments as $comment)
                        <div class="mb-3">
                            <p>{{ $comment->content }}</p>
                            <p><small>By {{ $comment->user->name }} on {{ $comment->created_at->format('d M Y, h:i A') }}</small></p>
                            @if(Auth::id() === $comment->user_id)
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</button>
                                </form>
                            @endif
                        </div>
                    @endforeach

                    <form action="{{ route('comments.store', $patient->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
