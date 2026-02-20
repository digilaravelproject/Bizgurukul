<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Skills Pehle') }} | The Future of Digital Learning</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/site_images/logo.png') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Configuration -->
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Outfit', 'sans-serif'],
                },
                extend: {
                    colors: {
                        primary: '#f7941d',
                        secondary: '#1a1a1a',
                        accent: '#ff5f1f',
                        surface: '#ffffff',
                        bodyBg: '#fffbf7',
                        muted: '#64748b',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

        body {
            background-color: #ffffff;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #ffffff; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white text-secondary selection:bg-primary/20" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-white border-b transition-colors duration-200"
         :class="scrolled ? 'border-gray-200 shadow-sm py-4' : 'border-transparent py-6'">
        <div class="container mx-auto px-6 max-w-7xl flex items-center justify-between">
            <a href="#" class="block focus:outline-none">
                <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Skills Pehle Logo" class="h-8 md:h-9 w-auto">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-10">
                <a href="#home" class="text-sm font-semibold text-gray-600 hover:text-secondary transition-colors">Home</a>

                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="text-sm font-semibold text-gray-600 hover:text-secondary transition-colors flex items-center gap-1 focus:outline-none">
                        Programs <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute top-full left-0 pt-4 w-56">
                        <div class="bg-white border border-gray-100 shadow-lg rounded-xl p-2 flex flex-col gap-1">
                            <a href="#" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-secondary hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                                <i class="fas fa-bolt text-gray-400"></i> Freelancing
                            </a>
                            <a href="#" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-secondary hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                                <i class="fas fa-rocket text-gray-400"></i> Business Mastery
                            </a>
                        </div>
                    </div>
                </div>

                <a href="#about" class="text-sm font-semibold text-gray-600 hover:text-secondary transition-colors">About</a>
                <a href="#refer" class="text-sm font-semibold text-gray-600 hover:text-secondary transition-colors">Refer & Earn</a>
            </div>

            <!-- Actions -->
            <div class="hidden lg:flex items-center space-x-6">
                @auth
                    @if(auth()->user()->hasRole('Admin'))
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-white bg-secondary px-6 py-2.5 rounded-lg hover:bg-black transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-secondary">Dashboard</a>
                    @elseif(auth()->user()->hasRole('Student'))
                        <a href="{{ route('student.dashboard') }}" class="text-sm font-bold text-white bg-secondary px-6 py-2.5 rounded-lg hover:bg-black transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-white bg-secondary px-6 py-2.5 rounded-lg hover:bg-black transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-secondary">Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-secondary transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="text-sm font-bold text-white bg-primary px-6 py-2.5 rounded-lg hover:bg-orange-500 transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-primary">Get Access</a>
                @endauth
            </div>

            <!-- Mobile Toggle -->
            <button @click="$dispatch('open-menu')" class="lg:hidden text-gray-600 hover:text-secondary focus:outline-none p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-data="{ open: false }" @open-menu.window="open = true" @keydown.escape.window="open = false" class="relative z-[100]" x-cloak>
        <div x-show="open" class="fixed inset-0 bg-secondary/40 backdrop-blur-sm transition-opacity" @click="open = false" x-transition.opacity></div>
        <div x-show="open" class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl flex flex-col" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="p-6 flex items-center justify-between border-b border-gray-100">
                <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo" class="h-8 w-auto">
                <button @click="open = false" class="p-2 text-gray-400 hover:text-secondary focus:outline-none"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 flex flex-col space-y-8">
                <div class="flex flex-col space-y-6">
                    <a href="#home" @click="open = false" class="text-lg font-bold text-secondary">Home</a>
                    <a href="#courses" @click="open = false" class="text-lg font-bold text-secondary">Programs</a>
                    <a href="#about" @click="open = false" class="text-lg font-bold text-secondary">About</a>
                    <a href="#refer" @click="open = false" class="text-lg font-bold text-secondary">Refer & Earn</a>
                </div>
                <div class="pt-8 border-t border-gray-100 flex flex-col space-y-4">
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="w-full text-center py-3.5 bg-secondary text-white font-bold rounded-xl">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-3.5 border border-gray-200 text-secondary font-bold rounded-xl hover:bg-gray-50">Login</a>
                        <a href="{{ route('register') }}" class="w-full text-center py-3.5 bg-primary text-white font-bold rounded-xl shadow-sm">Get Access</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="pt-40 pb-20 lg:pt-48 lg:pb-32 bg-gray-50/50">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid lg:grid-cols-2 gap-16 lg:gap-8 items-center">
                <!-- Content -->
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm mb-8">
                        <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">Empowering 100K+ Learners</span>
                    </div>

                    <h1 class="text-5xl lg:text-7xl font-black text-secondary leading-[1.05] tracking-tight mb-8">
                        Think Freelance,<br />
                        <span class="text-primary">Think Skills Pehle.</span>
                    </h1>

                    <p class="text-lg text-gray-600 leading-relaxed mb-10 font-medium">
                        Master the skills that the world is ready to pay for. Build a high-income career without the traditional 9-to-5 grind.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <a href="#courses" class="w-full sm:w-auto px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-orange-500 transition-colors text-center shadow-sm">
                            Start Your Journey
                        </a>
                        <button class="w-full sm:w-auto px-8 py-4 bg-white border border-gray-200 text-secondary font-bold rounded-xl hover:bg-gray-50 flex items-center justify-center gap-3 transition-colors">
                            <i class="fas fa-play text-primary text-sm"></i>
                            Watch Manifesto
                        </button>
                    </div>
                </div>

                <!-- Media -->
                <div class="relative lg:ml-auto w-full max-w-lg">
                    <div class="aspect-[4/5] rounded-3xl overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80" alt="Learning" class="w-full h-full object-cover">
                    </div>

                    <!-- Ranking Badge -->
                    <div class="absolute bottom-8 -left-8 bg-white p-5 rounded-2xl shadow-xl flex items-center gap-4 border border-gray-100 max-w-xs">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-chart-line text-lg text-primary"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Global Ranking</p>
                            <p class="text-lg font-bold text-secondary leading-none">#1 in India</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Placement Banner -->
    <section class="py-12 bg-white border-y border-gray-100">
        <div class="container mx-auto px-6 max-w-7xl">
            <p class="text-center text-xs font-bold text-gray-400 uppercase tracking-widest mb-8">Alumni Placed At</p>
            <div class="flex flex-wrap justify-center items-center gap-x-12 lg:gap-x-24 gap-y-8 grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                <span class="text-2xl font-black text-secondary tracking-tight">Meta</span>
                <span class="text-2xl font-bold text-secondary tracking-tight">Genpact</span>
                <span class="text-2xl font-black text-secondary tracking-tight">Zomato</span>
                <span class="text-2xl font-bold text-secondary tracking-tight">Deloitte</span>
                <span class="text-2xl font-black text-secondary tracking-tight">Wipro</span>
            </div>
        </div>
    </section>

    <!-- Course Deck -->
    <section id="courses" class="py-24 bg-gray-50/50">
        <div class="container mx-auto px-6 max-w-7xl text-center">
            <div class="max-w-2xl mx-auto mb-16 pt-8">
                <span class="text-primary font-bold uppercase tracking-widest text-xs mb-4 block">Expert Modules</span>
                <h2 class="text-4xl md:text-5xl font-black text-secondary tracking-tight">Choose Your Skill Path</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 text-left">
                @forelse($courses ?? [] as $course)
                @php /** @var \App\Models\Bundle|\App\Models\Course $course */ @endphp
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-gray-300 hover:shadow-lg transition-all duration-200 flex flex-col h-full group">
                    <div class="h-60 w-full relative overflow-hidden bg-gray-100">
                        <img src="{{ $course->thumbnail ?? 'https://via.placeholder.com/600x400' }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white px-3 py-1.5 rounded-lg text-xs font-bold text-secondary shadow-sm">
                            {{ $course->category->name ?? 'Premium' }}
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-1 text-primary text-xs mb-4">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="text-gray-500 font-bold ml-1.5">(12k+ Enrollments)</span>
                        </div>

                        <h3 class="text-xl font-bold text-secondary mb-3 leading-tight group-hover:text-primary transition-colors line-clamp-2">
                            {{ $course->title }}
                        </h3>

                        <p class="text-gray-500 text-sm mb-8 line-clamp-2 flex-1">
                            {{ strip_tags($course->description ?? 'Comprehensive learning module to elevate your skillset.') }}
                        </p>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-auto">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Fee</p>
                                <p class="text-2xl font-black text-secondary leading-none">₹{{ number_format($course->final_price ?? $course->price ?? 0) }}</p>
                            </div>
                            <a href="{{ route('course.show', $course->id) }}" class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-secondary hover:bg-secondary hover:text-white transition-colors">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-24 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">New Bundles Landing Soon</h3>
                    <p class="text-gray-500">We are currently curating the best content for you.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Metrics Section -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="bg-secondary rounded-[2.5rem] p-10 md:p-16 lg:p-20 overflow-hidden relative">
                <div class="grid lg:grid-cols-5 gap-16 lg:gap-8 items-center relative z-10">

                    <!-- Text Area -->
                    <div class="lg:col-span-3">
                        <h2 class="text-4xl lg:text-5xl font-black text-white leading-[1.1] tracking-tight mb-6">
                            The World is <br class="hidden md:block"/> Your Workplace.
                        </h2>
                        <p class="text-gray-400 text-lg mb-10 max-w-lg leading-relaxed">
                            We provide the blueprint to escape the matrix and build a life of freedom through digital mastery.
                        </p>
                        <a href="{{ route('register') }}" class="inline-block border-2 border-white/20 hover:border-white text-white px-8 py-4 rounded-xl font-bold transition-all focus:ring-2 focus:ring-white/50 focus:outline-none">
                            Launch Your Career
                        </a>
                    </div>

                    <!-- Stats Grid -->
                    <div class="lg:col-span-2 grid grid-cols-2 gap-x-8 gap-y-12">
                        <div>
                            <p class="text-4xl lg:text-5xl font-black text-white tracking-tight mb-2">250+</p>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Courses</p>
                        </div>
                        <div>
                            <p class="text-4xl lg:text-5xl font-black text-primary tracking-tight mb-2">15.8Cr</p>
                            <p class="text-xs font-bold text-primary/70 uppercase tracking-widest">Payouts Processed</p>
                        </div>
                        <div class="col-span-2 pt-8 border-t border-white/10 flex items-center gap-6">
                            <div class="flex -space-x-3 shrink-0">
                                <img class="w-12 h-12 rounded-full border-2 border-secondary object-cover" src="https://i.pravatar.cc/100?u=1" alt="Student">
                                <img class="w-12 h-12 rounded-full border-2 border-secondary object-cover" src="https://i.pravatar.cc/100?u=2" alt="Student">
                                <img class="w-12 h-12 rounded-full border-2 border-secondary object-cover" src="https://i.pravatar.cc/100?u=3" alt="Student">
                            </div>
                            <p class="text-sm font-bold text-gray-300">Join 1M+ active digital warriors worldwide</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 pt-20 pb-10 border-t border-gray-200">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8 mb-16">
                <!-- Branding -->
                <div class="lg:col-span-2">
                    <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Skills Pehle" class="h-8 w-auto mb-8">
                    <h3 class="text-2xl font-bold text-secondary mb-8 max-w-sm tracking-tight leading-snug">
                        Empowering the next Generation.
                    </h3>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-secondary hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-secondary hover:text-white transition-colors"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center text-gray-600 hover:bg-secondary hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Nav 1 -->
                <div>
                    <h4 class="text-sm font-bold text-secondary mb-6 tracking-wide">Navigation</h4>
                    <ul class="space-y-4">
                        <li><a href="#courses" class="text-gray-500 hover:text-secondary font-medium transition-colors">Top Courses</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Join as Mentor</a></li>
                        <li><a href="#refer" class="text-gray-500 hover:text-secondary font-medium transition-colors">Referral Portal</a></li>
                    </ul>
                </div>

                <!-- Nav 2 -->
                <div>
                    <h4 class="text-sm font-bold text-secondary mb-6 tracking-wide">Legal</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Terms of Use</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Refund Terms</a></li>
                    </ul>
                </div>

                <!-- Nav 3 -->
                <div>
                     <h4 class="text-sm font-bold text-secondary mb-6 tracking-wide">Company</h4>
                     <ul class="space-y-4">
                        <li><a href="#about" class="text-gray-500 hover:text-secondary font-medium transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Contact</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-secondary font-medium transition-colors">Careers</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="pt-8 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500 font-medium">© 2026 Skills Pehle Ecosystem. Global Inc.</p>
                <div class="flex gap-6 text-sm text-gray-500 font-medium">
                    <span>GSTIN: 09XXXXXXXXXXXX</span>
                    <span>PATNA, BIHAR</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
