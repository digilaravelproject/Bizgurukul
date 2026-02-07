<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizgurukul - Digital Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f7941d',
                        secondary: '#1a1a1a',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #d4f5d4 0%, #e8f8e8 100%);
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="scroll-smooth">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('storage/site_images/logo1.png') }}"
                        alt="Logo" class="w-20 h-auto" loading="lazy">
                    {{-- <span class="text-2xl font-bold text-secondary">Skills Pehle</span> --}}
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-primary transition">Home</a>
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-primary transition flex items-center">
                            Our Courses <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute hidden group-hover:block w-48 bg-white shadow-lg rounded-lg mt-2 py-2">
                            <a href="#courses" class="block px-4 py-2 hover:bg-gray-100">Digital Freelancing</a>
                            <a href="#courses" class="block px-4 py-2 hover:bg-gray-100">Entrepreneurship</a>
                            <a href="#courses" class="block px-4 py-2 hover:bg-gray-100">Upskilling</a>
                        </div>
                    </div>
                    <a href="#about" class="text-gray-700 hover:text-primary transition">About Us</a>
                    <a href="#refer" class="text-gray-700 hover:text-primary transition">Refer & Earn</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary transition font-semibold">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition">Sign
                        Up</a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="md:hidden pb-4">
                <a href="#home" class="block py-2 text-gray-700 hover:text-primary">Home</a>
                <a href="#courses" class="block py-2 text-gray-700 hover:text-primary">Our Courses</a>
                <a href="#about" class="block py-2 text-gray-700 hover:text-primary">About Us</a>
                <a href="#refer" class="block py-2 text-gray-700 hover:text-primary">Refer & Earn</a>
                <div class="flex flex-col space-y-2 mt-4">
                    <button class="text-gray-700 hover:text-primary transition font-semibold">Log in</button>
                    <button class="bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold">Sign
                        Up</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-24 pb-16 gradient-bg">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <h1 class="text-4xl md:text-6xl font-bold text-secondary mb-4">
                        Think Freelance<br>
                        Think <span class="text-primary">Skills Pehle</span>
                    </h1>
                    <p class="text-gray-600 text-lg mb-6">
                        Master digital skills, build your freelancing career, and earn from anywhere in the world.
                    </p>
                    <button
                        class="bg-primary hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold text-lg transition hover-scale">
                        Enroll Now
                    </button>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23f0f0f0' width='400' height='300' rx='10'/%3E%3Crect fill='%23f7941d' x='50' y='50' width='300' height='200' rx='5'/%3E%3Ctext x='200' y='160' font-size='30' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'%3ELearn%3C/text%3E%3C/svg%3E"
                        alt="Hero Image" class="max-w-md w-full rounded-lg shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Trusted Companies -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-center text-2xl font-semibold text-gray-700 mb-8">From Us To The Industry</h2>
            <div class="flex flex-wrap justify-center items-center gap-8" x-data="{ companies: ['SPACE', 'Genpact', 'Zomato', 'Wipro', 'Flydocs', 'Deloitte'] }">
                <template x-for="company in companies" :key="company">
                    <div class="text-gray-400 text-xl font-semibold" x-text="company"></div>
                </template>
            </div>
        </div>
    </section>

    <!-- Personal Finance Banner -->
    <section class="py-16 gradient-bg">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 p-8 md:p-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-4">
                            Skills Pehle has just launched<br>
                            Mastering Personal <span class="text-green-500">Finance!</span>
                        </h2>
                        <button
                            class="bg-white border-2 border-green-500 text-green-500 hover:bg-green-500 hover:text-white px-6 py-2 rounded-full font-semibold transition mt-4">
                            Know More
                        </button>
                    </div>
                    <div class="md:w-1/2 p-8 gradient-bg">
                        <div class="text-center">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect fill='%234ade80' width='300' height='300' rx='150'/%3E%3Ctext x='150' y='170' font-size='80' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'%3E₹%3C/text%3E%3C/svg%3E"
                                alt="Finance" class="w-48 h-48 mx-auto mb-4">
                            <h3 class="text-2xl font-bold text-secondary">Take charge of your Money,</h3>
                            <p class="text-gray-600">build wealth, and secure your future.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WhatsApp CTA -->
    <div class="bg-green-500 py-3">
        <div class="container mx-auto px-4 flex items-center justify-center text-white">
            <i class="fab fa-whatsapp text-2xl mr-3"></i>
            <span class="font-semibold">Follow Our Official WhatsApp Channel</span>
        </div>
    </div>

    <!-- Courses Section -->
    <section id="courses" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-secondary mb-12">Our Courses</h2>

            <!-- Digital Freelancing Bundles -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-center text-primary mb-8">Digital Freelancing Bundles</h3>
                <div class="grid md:grid-cols-3 gap-8">
                    @forelse($courses as $course)
                        <div
                            class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale transition-transform duration-300 hover:scale-105">
                            {{-- Course Thumbnail --}}
                            <div class="h-48 flex items-center justify-center overflow-hidden bg-gray-100">
                                @if ($course->thumbnail)
                                    <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                                        class="w-full h-full object-cover">
                                @else
                                    {{-- Fallback Gradient if no image --}}
                                    <div
                                        class="w-full h-full bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-2xl p-4 text-center">
                                        {{ $course->title }}
                                    </div>
                                @endif
                            </div>

                            {{-- Course Details --}}
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-xl font-bold text-mainText truncate mr-2">{{ $course->title }}</h4>
                                    <div class="text-right">
                                        {{-- Price Logic: Agar final_price hai toh wo dikhao, nahi toh original price --}}
                                        <span class="text-primary font-bold block">
                                            ₹{{ number_format($course->final_price > 0 ? $course->final_price : $course->price, 0) }}
                                        </span>
                                        {{-- Agar discount available hai toh purani price strike-through dikhao --}}
                                        @if ($course->final_price > 0 && $course->final_price < $course->price)
                                            <span
                                                class="text-gray-400 line-through text-xs italic">₹{{ number_format($course->price, 0) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ Str::limit(strip_tags($course->description), 80) }}
                                </p>

                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full uppercase">
                                        {{ $course->category->name ?? 'Course' }}
                                    </span>
                                </div>

                                <a href="{{ route('course.show', $course->id) }}"
                                    class="block text-center bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition duration-300">
                                    Enroll Now
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10">
                            <p class="text-gray-500 italic">No courses found at the moment.</p>
                        </div>
                    @endforelse
                </div>
                {{-- Optional: Pagination Links --}}
                <div class="mt-8">
                    {{ $courses->links() }}
                </div>
            </div>

            <!-- Digital Entrepreneurship Bundles -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-center text-primary mb-8">Digital Entrepreneurship Bundle</h3>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-red-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-bullhorn text-4xl text-red-500"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Marketing Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-green-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-tag text-4xl text-green-500"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Branding Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-blue-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-chart-line text-4xl text-blue-500"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Traffic Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-8 mt-8">
                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-purple-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-users text-4xl text-purple-500"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Influence Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-yellow-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-dollar-sign text-4xl text-yellow-600"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Finance Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6 hover-scale">
                        <div class="bg-indigo-100 rounded-lg p-4 mb-4 text-center">
                            <i class="fas fa-briefcase text-4xl text-indigo-500"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-2">Business Mastery</h4>
                        <button
                            class="bg-primary hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold w-full transition mt-4">
                            Enroll Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Upskilling Courses -->
            <div>
                <h3 class="text-2xl font-bold text-center text-primary mb-8">Upskilling Courses</h3>
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-code text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Development</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-chart-bar text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Marketing</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-building text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Business</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-leaf text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Lifestyle</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-wallet text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Finance</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-heartbeat text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Health and Fitness</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-user text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Personal Development</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-music text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Music</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-palette text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Design</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-camera text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Photography and Videography</h4>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-4 hover-scale text-center">
                        <i class="fas fa-laptop text-4xl text-primary mb-2"></i>
                        <h4 class="font-semibold">Office Productivity</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-secondary mb-12">Why Choose Skills Pehle?</h2>
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let interval = setInterval(() => {
                        if (count < 250) count += 5;
                        else clearInterval(interval);
                    }, 20);
                }, 200)">
                    <div class="text-5xl font-bold text-primary mb-2">
                        <span x-text="count + '+'"></span>
                    </div>
                    <p class="text-gray-600 font-semibold">Courses Available</p>
                </div>
                <div x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let interval = setInterval(() => {
                        if (count < 180) count += 3;
                        else clearInterval(interval);
                    }, 20);
                }, 200)">
                    <div class="text-5xl font-bold text-primary mb-2">
                        <span x-text="count + '+'"></span>
                    </div>
                    <p class="text-gray-600 font-semibold">Expert Instructors</p>
                </div>
                <div x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let interval = setInterval(() => {
                        if (count < 390) count += 7;
                        else clearInterval(interval);
                    }, 20);
                }, 200)">
                    <div class="text-5xl font-bold text-primary mb-2">
                        <span x-text="count + '+'"></span>
                    </div>
                    <p class="text-gray-600 font-semibold">Students Enrolled</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 gradient-bg">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-secondary mb-12">What Our Students Say</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle fill='%23f7941d' cx='50' cy='50' r='50'/%3E%3Ctext x='50' y='65' font-size='40' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'%3EA%3C/text%3E%3C/svg%3E"
                            alt="Student" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Amit Kumar</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Best platform to learn digital marketing and freelancing. The courses are
                        very practical and easy to understand."</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle fill='%23f7941d' cx='50' cy='50' r='50'/%3E%3Ctext x='50' y='65' font-size='40' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'%3EP%3C/text%3E%3C/svg%3E"
                            alt="Student" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Priya Sharma</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"I started earning within 2 months of completing the course. Highly
                        recommended for anyone looking to start freelancing!"</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle fill='%23f7941d' cx='50' cy='50' r='50'/%3E%3Ctext x='50' y='65' font-size='40' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'%3ER%3C/text%3E%3C/svg%3E"
                            alt="Student" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Rahul Singh</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Amazing content and support. The mentors are always available to help.
                        Worth every penny!"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Ready to Start Your Journey?</h2>
            <p class="text-white text-lg mb-8">Join thousands of students already learning on Skills Pehle</p>
            <button
                class="bg-white hover:bg-gray-100 text-primary px-8 py-3 rounded-lg font-semibold text-lg transition hover-scale">
                Get Started Today
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Skills Pehle</h3>
                    <p class="text-gray-400">Empowering learners with digital skills for the future.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-primary hover:text-orange-600 transition"><i
                                class="fab fa-facebook-f text-xl"></i></a>
                        <a href="#" class="text-primary hover:text-orange-600 transition"><i
                                class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-primary hover:text-orange-600 transition"><i
                                class="fab fa-instagram text-xl"></i></a>
                        <a href="#" class="text-primary hover:text-orange-600 transition"><i
                                class="fab fa-linkedin-in text-xl"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Courses</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Terms of Service</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Privacy Policy</a>
                        </li>
                        <li><a href="#" class="text-gray-400 hover:text-primary transition">Refund Policy</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Subscribe to get updates on new courses and offers.</p>
                    <div class="flex">
                        <input type="email" placeholder="Enter your email"
                            class="flex-1 px-4 py-2 rounded-l-lg text-gray-800">
                        <button class="bg-primary hover:bg-orange-600 px-4 py-2 rounded-r-lg transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Skills Pehle. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop"
        class="fixed bottom-8 right-8 bg-primary hover:bg-orange-600 text-white w-12 h-12 rounded-full shadow-lg hidden items-center justify-center transition hover-scale z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // jQuery smooth scroll
        $(document).ready(function() {
            // Smooth scrolling for nav links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });

            // Scroll to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#scrollTop').fadeIn().css('display', 'flex');
                } else {
                    $('#scrollTop').fadeOut();
                }
            });

            $('#scrollTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

            // Add animation on scroll
            $(window).scroll(function() {
                $('.hover-scale').each(function() {
                    var position = $(this).offset().top;
                    var scroll = $(window).scrollTop();
                    var windowHeight = $(window).height();

                    if (scroll + windowHeight > position + 100) {
                        $(this).addClass('animate__animated animate__fadeInUp');
                    }
                });
            });
        });
    </script>
</body>

</html>
