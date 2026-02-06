<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Bizgurukul</title>
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

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        .course-thumbnail {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-md fixed w-full top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <a href="/" class="flex items-center space-x-2">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23f7941d' width='100' height='100' rx='10'/%3E%3Ctext x='50' y='65' font-size='50' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-weight='bold'%3EB%3C/text%3E%3C/svg%3E"
                            alt="Logo" class="w-10 h-10">
                        <span class="text-2xl font-bold text-secondary">Bizgurukul</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-primary transition">Home</a>
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-primary transition flex items-center">
                            Our Courses <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute hidden group-hover:block w-48 bg-white shadow-lg rounded-lg mt-2 py-2">
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Digital Freelancing</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Entrepreneurship</a>
                            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Upskilling</a>
                        </div>
                    </div>
                    <a href="#" class="text-gray-700 hover:text-primary transition">About Us</a>
                    <a href="#" class="text-gray-700 hover:text-primary transition">Refer & Earn</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary transition font-semibold">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition">Sign
                        Up</a>
                </div>

                <button @click="mobileMenu = !mobileMenu" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <div x-show="mobileMenu" x-transition class="md:hidden pb-4">
                <a href="/" class="block py-2 text-gray-700 hover:text-primary">Home</a>
                <a href="#" class="block py-2 text-gray-700 hover:text-primary">Our Courses</a>
                <div class="flex flex-col space-y-2 mt-4">
                    <button class="text-gray-700 hover:text-primary transition font-semibold">Log in</button>
                    <button class="bg-primary hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold">Sign
                        Up</button>
                </div>
            </div>
        </div>
    </nav>

    <section class="pt-28 pb-12 bg-white">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-secondary mb-8">{{ $course->title }}</h1>

            <div class="relative rounded-2xl overflow-hidden shadow-2xl mb-8"
                style="background: linear-gradient(to right, #2d1b3d 0%, #2d1b3d 50%, #d4949c 50%, #d4949c 100%);">
                <div class="flex items-center justify-center p-8 md:p-16 min-h-[300px]">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 max-w-xs w-full">
                        <div class="bg-gradient-to-b from-pink-200 to-pink-300 rounded-lg p-6 text-center">
                            @if ($course->thumbnail)
                                <img src="{{ $course->thumbnail }}"
                                    class="w-24 h-24 mx-auto mb-4 rounded-lg object-cover shadow-md">
                            @endif
                            <h3 class="text-xl font-bold text-pink-600 mb-4 uppercase">{{ $course->title }}</h3>
                            <div class="flex justify-center space-x-2 mb-4">
                                <i class="fab fa-facebook text-blue-600"></i>
                                <i class="fab fa-instagram text-pink-600"></i>
                                <i class="fab fa-youtube text-red-600"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-center mx-auto mb-2 w-16 h-16">
                        <i class="fas fa-video text-blue-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-secondary">{{ $course->lessons->count() }}</div>
                    <div class="text-gray-600">Lessons</div>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-center mx-auto mb-2 w-16 h-16">
                        <i class="fas fa-clock text-blue-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-secondary">15+</div>
                    <div class="text-gray-600">Hours</div>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-center mx-auto mb-2 w-16 h-16">
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-secondary">2 Lakh+</div>
                    <div class="text-gray-600">Students</div>
                </div>
                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-lg flex items-center justify-center mx-auto mb-2 w-16 h-16">
                        <i class="fas fa-certificate text-blue-500 text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-secondary">Bizgurukul</div>
                    <div class="text-gray-600">Certificate</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-secondary mb-6">
                        {{ $course->category->name ?? 'Course Detail' }}</h2>
                    <p class="text-lg font-semibold text-gray-700 mb-4">By Instructor</p>
                    <div class="text-gray-700 leading-relaxed mb-6 prose">
                        {!! $course->description !!}
                    </div>

                    <div class="mb-8">
                        <span
                            class="text-4xl font-bold text-primary">₹{{ number_format($course->final_price, 0) }}</span>
                        @if ($course->final_price < $course->price)
                            <span
                                class="text-gray-400 line-through ml-3 text-xl">₹{{ number_format($course->price, 0) }}</span>
                        @endif
                    </div>

                    @if ($isBundleCourse)
                        <div class="bg-orange-50 p-6 rounded-xl border border-primary/20 mb-6">
                            <p class="text-secondary font-bold mb-3">Save more with our Bundle Offer!</p>
                            <a href="{{ route('bundles.show', $bundle->id) }}"
                                class="bg-secondary text-white px-10 py-3 rounded-lg font-bold hover:bg-black transition inline-block">
                                View {{ $bundle->name }} Bundle
                            </a>
                        </div>
                    @else
                        <form action="{{ route('payment.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <button type="submit"
                                class="bg-primary hover:bg-orange-600 text-white px-12 py-4 rounded-lg font-bold text-xl shadow-lg hover-scale transition">
                                Buy this course Now
                            </button>
                        </form>
                    @endif
                </div>

                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                        @if ($course->demo_video_url)
                            <video controls class="w-full" poster="{{ $course->thumbnail }}">
                                <source src="{{ $course->demo_video_url }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ $course->thumbnail }}" class="w-full">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-100 pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23f7941d' width='100' height='100' rx='10'/%3E%3Ctext x='50' y='65' font-size='50' text-anchor='middle' fill='white' font-family='Arial, sans-serif' font-weight='bold'%3EB%3C/text%3E%3C/svg%3E"
                            alt="Logo" class="w-10 h-10">
                        <span class="text-xl font-bold text-secondary">Bizgurukul</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Download the Bizgurukul app for more features.</p>
                </div>
                <div>
                    <h4 class="font-bold text-secondary mb-4">Bundles</h4>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li><a href="#" class="hover:text-primary transition">Upskilling Courses</a></li>
                        <li><a href="#" class="hover:text-primary transition">Entrepreneurship Bundles</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-secondary mb-4">Useful Links</h4>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li><a href="#" class="hover:text-primary transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-primary transition">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-secondary mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-600 text-sm">
                        <li><a href="#" class="hover:text-primary transition">About us</a></li>
                        <li><a href="#" class="hover:text-primary transition">Refer & Earn</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">All Rights Reserved © 2025 | BIZGURUKUL PRIVATE LIMITED</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-600 hover:text-primary"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-600 hover:text-primary"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-600 hover:text-primary"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/your-number" target="_blank"
        class="fixed bottom-8 right-8 bg-green-500 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center z-50 hover-scale transition">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>

    <div class="fixed bottom-0 left-0 right-0 bg-green-500 text-white py-3 z-40 text-center">
        <span class="font-semibold"><i class="fab fa-whatsapp mr-2"></i>Follow Our Official WhatsApp Channel</span>
    </div>

    <script>
        $(document).ready(function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });
        });
    </script>
</body>

</html>
