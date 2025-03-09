Laravel Patient Registration System

This project is a Patient Registration System built using the Laravel framework. It allows users to register, manage patient records, and add comments to patient profiles. The system is designed to be modern and responsive, utilizing Bootstrap for styling and layout.

#### Features:
- User Authentication (Login, Register)
- Patient Management (Create, Read, Update, Delete)
- Commenting System for Patient Profiles
- Responsive Design with Bootstrap
- Modern UI with 3D Background Animation using Three.js and GSAP

![image](https://github.com/user-attachments/assets/19299d82-86bd-440d-886f-9bc28ecdb4d0)
![image](https://github.com/user-attachments/assets/2361d098-c92c-4d59-bbcd-c0ad155b7a76)


### Project Setup

#### 1. Create a new Laravel project
```bash
composer create-project laravel/laravel patient-registration-system
cd patient-registration-system
```

#### 2. Set up the database configuration
Edit the .env file to configure MySQL:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=patient_registration
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 3. Install Laravel UI package for authentication
```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run dev
```

### Database Structure

#### 4. Create migrations
Create the patients table:
```bash
php artisan make:migration create_patients_table
```
Edit the migration file (`database/migrations/xxxx_xx_xx_create_patients_table.php`):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->text('medical_history')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
```

Create the comments table:
```bash
php artisan make:migration create_comments_table
```
Edit the migration file:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
```

#### 5. Run migrations
```bash
php artisan migrate
```

### Models

#### 6. Create models
User model already exists, so let's create Patient and Comment models:
```bash
php artisan make:model Patient
php artisan make:model Comment
```

Edit the Patient model (`app/Models/Patient.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'medical_history',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

Edit the Comment model (`app/Models/Comment.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'patient_id',
        'user_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Update the User model (`app/Models/User.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

### Controllers

#### 7. Create controllers
```bash
php artisan make:controller PatientController --resource
php artisan make:controller CommentController
php artisan make:controller DashboardController
```

Edit the PatientController (`app/Http/Controllers/PatientController.php`):
```php
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
```

Edit the CommentController (`app/Http/Controllers/CommentController.php`):
```php
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
```

Edit the DashboardController (`app/Http/Controllers/DashboardController.php`):
```php
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
```

### Routes

#### 8. Set up routes
Edit web.php:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Auth routes (already set up by Laravel UI)
Auth::routes();

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Patient routes
Route::resource('patients', PatientController::class);

// Comment routes
Route::post('/patients/{patient}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Redirect after login
Route::get('/home', function() {
    return redirect()->route('dashboard');
});
```

### Views

#### 9. Create layouts and views
Create the following view files:

**resources/views/layouts/app.blade.php** (already created by Laravel UI, just modify it):
```html
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Patient Registration System') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Patient Registration System') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('patients.index') }}">Patients</a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

**resources/views/welcome.blade.php**:
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Registration</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            color: white;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .card-container {
            perspective: 1000px;
        }

        .patient-card {
            width: 400px;
            height: 250px;
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transform-style: preserve-3d;
            transition: transform 0.5s ease;
        }

        .patient-card:hover {
            transform: rotateY(10deg) rotateX(10deg);
        }

        .register-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .register-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<!-- 3D Background Effect -->
<div class="canvas-container">
    <canvas id="bgCanvas"></canvas>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-danger" href="#">Patient Registration</a>
        <div class="ms-auto">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-outline-danger">Home</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-danger me-2">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-danger">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<!-- Patient Card -->
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card-container">
        <div class="patient-card">
            <h2 class="mb-3">Patient Registration</h2>
            <p>Welcome to our system! Register now to get started.</p>
            <a href="{{ route('register') }}" class="btn register-btn">Register Now</a>
        </div>
    </div>
</div>

<!-- Three.js and GSAP Animation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    // THREE.js Background Animation
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("bgCanvas"),<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Registration</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            color: white;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        .canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .card-container {
            perspective: 1000px;
        }

        .patient-card {
            width: 400px;
            height: 250px;
            background: linear-gradient(135deg, #1abc9c, #16a085);
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            transform-style: preserve-3d;
            transition: transform 0.5s ease;
        }

        .patient-card:hover {
            transform: rotateY(10deg) rotateX(10deg);
        }

        .register-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .register-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<!-- 3D Background Effect -->
<div class="canvas-container">
    <canvas id="bgCanvas"></canvas>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-danger" href="#">Patient Registration</a>
        <div class="ms-auto">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-outline-danger">Home</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-danger me-2">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-danger">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<!-- Patient Card -->
<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card-container">
        <div class="patient-card">
            <h2 class="mb-3">Patient Registration</h2>
            <p>Welcome to our system! Register now to get started.</p>
            <a href="{{ route('register') }}" class="btn register-btn">Register Now</a>
        </div>
    </div>
</div>

<!-- Three.js and GSAP Animation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    // THREE.js Background Animation
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("bgCanvas"),

Similar code found with 2 license types
