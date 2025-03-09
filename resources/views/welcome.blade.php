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
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("bgCanvas"), alpha: true });

    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const geometry = new THREE.SphereGeometry(0.2, 32, 32);
    const material = new THREE.MeshStandardMaterial({ color: 0xffffff });

    const particles = [];
    for (let i = 0; i < 100; i++) {
        let particle = new THREE.Mesh(geometry, material);
        particle.position.set(
            (Math.random() - 0.5) * 10,
            (Math.random() - 0.5) * 10,
            (Math.random() - 0.5) * 10
        );
        scene.add(particle);
        particles.push(particle);
    }

    const light = new THREE.PointLight(0xffffff, 1);
    light.position.set(5, 5, 5);
    scene.add(light);

    camera.position.z = 5;

    function animate() {
        requestAnimationFrame(animate);
        particles.forEach(p => p.position.y += Math.random() * 0.005);
        renderer.render(scene, camera);
    }

    animate();

    // GSAP Animation for Card
    gsap.to(".patient-card", { scale: 1.05, yoyo: true, repeat: -1, duration: 1, ease: "power1.inOut" });
</script>

</body>
</html>
