<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Skills Pehle') }} | Future of Digital Learning</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/site_images/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f7941d',
                        secondary: '#1a1a1a',
                        accent: '#ff5f1f',
                        surface: '#ffffff',
                        bodyBg: '#fffbf7'
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
            font-family: 'Outfit', sans-serif;
            background-color: #fffbf7;
            overflow-x: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(247, 148, 29, 0.1);
        }

        .brand-gradient {
            background: linear-gradient(135deg, #f7941d 0%, #ff5f1f 100%);
        }

        .hero-shape {
            clip-path: polygon(0% 0%, 100% 0%, 100% 90%, 0% 100%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #fffbf7; }
        ::-webkit-scrollbar-thumb { background: #f7941d; border-radius: 10px; }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ atTop: true }" @scroll.window="atTop = (window.pageYOffset > 50 ? false : true)">

    <nav class="fixed w-full top-0 z-[100] transition-all duration-500"
         :class="atTop ? 'py-6' : 'py-3 glass shadow-lg'">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="#" class="flex items-center group">
                <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo" class="w-32 h-auto transition-transform group-hover:scale-110">
            </a>

            <div class="hidden lg:flex items-center space-x-10">
                <a href="#home" class="text-sm font-bold uppercase tracking-widest hover:text-primary transition-all">Home</a>
                <div class="relative group cursor-pointer" x-data="{ open: false }">
                    <button @mouseenter="open = true" class="text-sm font-bold uppercase tracking-widest hover:text-primary transition flex items-center">
                        Programs <i class="fas fa-chevron-down ml-2 text-[10px]"></i>
                    </button>
                    <div x-show="open" @mouseleave="open = false" x-cloak x-transition.opacity
                         class="absolute top-full -left-4 w-64 bg-white shadow-2xl rounded-2xl p-4 mt-2 border border-orange-50">
                        <a href="#" class="flex items-center p-3 hover:bg-orange-50 rounded-xl transition group">
                            <div class="w-10 h-10 rounded-lg brand-gradient flex items-center justify-center text-white mr-3 shadow-md">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <span class="font-bold text-sm">Freelancing</span>
                        </a>
                        <a href="#" class="flex items-center p-3 hover:bg-orange-50 rounded-xl transition group mt-2">
                            <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center text-white mr-3">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <span class="font-bold text-sm">Business Mastery</span>
                        </a>
                    </div>
                </div>
                <a href="#about" class="text-sm font-bold uppercase tracking-widest hover:text-primary transition">About</a>
                <a href="#refer" class="text-sm font-bold uppercase tracking-widest hover:text-primary transition">Refer & Earn</a>
            </div>

            <div class="hidden lg:flex items-center space-x-6">
                <a href="{{ route('login') }}" class="text-sm font-black uppercase tracking-widest hover:text-primary transition">Login</a>
                <a href="{{ route('register') }}"
                   class="brand-gradient text-white px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-[2px] shadow-lg shadow-primary/30 hover:shadow-primary/50 transition-all hover:-translate-y-1">
                    Get Access
                </a>
            </div>

            <button @click="$dispatch('toggle-menu')" class="lg:hidden text-2xl text-secondary">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>
    </nav>

    <section id="home" class="relative pt-40 lg:pt-56 pb-24 overflow-hidden hero-shape bg-white">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-orange-50/50 -z-10 rounded-bl-[100px]"></div>

        <div class="container mx-auto px-6 relative">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-3/5 text-center lg:text-left">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-orange-100 text-primary text-xs font-black uppercase tracking-[3px] mb-8 animate__animated animate__fadeInDown">
                        <span class="flex h-2 w-2 rounded-full bg-primary mr-3 animate-pulse"></span>
                        Empowering 100K+ Learners
                    </span>
                    <h1 class="text-6xl lg:text-[100px] font-black leading-[0.85] mb-10 tracking-tighter text-secondary animate__animated animate__fadeInLeft">
                        Think Freelance,<br>Think <span class="text-primary italic">Skills Pehle.</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-gray-500 max-w-xl mb-12 font-medium leading-relaxed animate__animated animate__fadeInUp">
                        Master the skills that the world is ready to pay for. Build a high-income career without the traditional 9-to-5 grind.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-6 justify-center lg:justify-start">
                        <a href="#courses" class="brand-gradient text-white px-10 py-5 rounded-[2rem] font-black text-sm uppercase tracking-[3px] shadow-2xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all">
                            Start Your Journey
                        </a>
                        <button class="flex items-center gap-4 font-black uppercase text-[10px] tracking-widest group">
                            <span class="w-16 h-16 rounded-full border-2 border-primary/20 flex items-center justify-center group-hover:border-primary transition-all">
                                <i class="fas fa-play text-primary ml-1"></i>
                            </span>
                            Watch Manifesto
                        </button>
                    </div>
                </div>

                <div class="lg:w-2/5 relative animate__animated animate__fadeInRight">
                    <div class="relative z-10 rounded-[3rem] overflow-hidden shadow-[0_50px_100px_-20px_rgba(247,148,29,0.3)]">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80"
                             alt="Learning" class="w-full h-auto object-cover hover:scale-110 transition-transform duration-1000">
                    </div>
                    <div class="absolute -bottom-10 -left-10 glass p-6 rounded-[2rem] shadow-2xl z-20 animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-500 flex items-center justify-center text-white">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Global Ranking</p>
                                <p class="text-xl font-black text-secondary">#1 in India</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white overflow-hidden">
        <div class="container mx-auto px-6">
            <p class="text-center text-[10px] font-black text-gray-400 uppercase tracking-[5px] mb-12">Alumni Placed At</p>
            <div class="flex flex-wrap justify-center items-center gap-12 lg:gap-24 opacity-30 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-1000">
                <span class="text-3xl font-black tracking-tighter uppercase italic">Meta</span>
                <span class="text-3xl font-black tracking-tighter uppercase">Genpact</span>
                <span class="text-3xl font-black tracking-tighter uppercase italic text-primary">Zomato</span>
                <span class="text-3xl font-black tracking-tighter uppercase font-serif">Deloitte</span>
                <span class="text-3xl font-black tracking-tighter uppercase">Wipro</span>
            </div>
        </div>
    </section>

    <section id="courses" class="py-32 bg-bodyBg">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-[11px] font-black text-primary uppercase tracking-[5px] mb-6">Expert Modules</h2>
            <h3 class="text-5xl lg:text-7xl font-black text-secondary tracking-tighter leading-none mb-24 uppercase">Choose Your <br> <span class="text-primary italic">Skill Path.</span></h3>

            <div class="grid lg:grid-cols-3 gap-10">
                @forelse($courses as $course)
                <div class="group relative bg-white rounded-[3rem] p-6 border border-orange-50 hover:border-primary/20 hover:shadow-[0_40px_80px_-20px_rgba(247,148,29,0.15)] transition-all duration-500 hover:-translate-y-4 overflow-hidden">
                    <div class="relative h-64 w-full rounded-[2rem] overflow-hidden mb-8">
                        <img src="{{ $course->thumbnail ?? 'https://via.placeholder.com/400x500' }}"
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest text-primary shadow-sm border border-orange-50">
                                {{ $course->category->name ?? 'Premium' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-left px-4 pb-4">
                        <div class="flex items-center text-primary text-[10px] mb-3 gap-1">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="text-gray-400 font-bold ml-2">(12k+ Enrollments)</span>
                        </div>
                        <h4 class="text-3xl font-black text-secondary leading-tight mb-4 tracking-tighter group-hover:text-primary transition-colors italic uppercase">
                            {{ $course->title }}
                        </h4>
                        <p class="text-gray-400 font-medium text-sm mb-10 line-clamp-2 leading-relaxed italic">
                            {{ strip_tags($course->description) }}
                        </p>

                        <div class="flex items-center justify-between pt-8 border-t border-orange-50">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Fee</p>
                                <p class="text-3xl font-black text-secondary">₹{{ number_format($course->final_price ?? $course->price) }}</p>
                            </div>
                            <a href="{{ route('course.show', $course->id) }}"
                               class="brand-gradient w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl shadow-primary/20 hover:rotate-12 transition-all">
                                <i class="fas fa-arrow-right text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 py-20 opacity-20 italic font-black uppercase tracking-widest">New Bundles Landing Soon...</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-32">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2 bg-secondary p-12 rounded-[3rem] flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute inset-0 brand-gradient opacity-0 group-hover:opacity-10 transition-opacity duration-700"></div>
                    <div class="relative z-10">
                        <h2 class="text-5xl font-black text-white tracking-tighter leading-none mb-8 italic uppercase">The World is <br> Your Workplace.</h2>
                        <p class="text-gray-400 font-medium text-lg max-w-sm mb-12">We provide the blueprint to escape the matrix and build a life of freedom through digital mastery.</p>
                        <a href="#" class="inline-block bg-white text-secondary px-10 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-primary hover:text-white transition-all shadow-2xl shadow-white/5">Launch Your Career</a>
                    </div>
                </div>

                <div class="glass p-12 rounded-[3rem] text-center flex flex-col justify-center gap-2 border-orange-50">
                    <p class="text-5xl font-black text-primary italic tracking-tighter">250+</p>
                    <p class="text-[10px] font-black uppercase tracking-[3px] text-gray-400">Total Courses</p>
                </div>

                <div class="bg-primary p-12 rounded-[3rem] text-center flex flex-col justify-center gap-2 text-white shadow-[0_20px_40px_rgba(247,148,29,0.3)]">
                    <p class="text-5xl font-black italic tracking-tighter">15.8Cr</p>
                    <p class="text-[10px] font-black uppercase tracking-[3px] text-white/70">Payouts Processed</p>
                </div>

                <div class="lg:col-span-2 bg-orange-100 p-12 rounded-[3rem] flex items-center gap-8 relative overflow-hidden">
                    <div class="flex -space-x-4">
                        <img class="w-16 h-16 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=1">
                        <img class="w-16 h-16 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=2">
                        <img class="w-16 h-16 rounded-full border-4 border-white" src="https://i.pravatar.cc/100?u=3">
                    </div>
                    <p class="text-secondary font-black uppercase text-xs italic tracking-widest">Join 1M+ active digital warriors</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white pt-32 pb-12 border-t border-orange-50">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-4 gap-20 mb-24 text-center lg:text-left">
                <div class="lg:col-span-2">
                    <img src="{{ asset('storage/site_images/logo1.png') }}" class="w-28 mb-10 mx-auto lg:mx-0">
                    <h3 class="text-4xl font-black text-secondary tracking-tighter leading-none mb-10 max-w-sm uppercase italic">Empowering the next <span class="text-primary">Generation.</span></h3>
                    <div class="flex justify-center lg:justify-start gap-4">
                        <a href="#" class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-primary hover:brand-gradient hover:text-white transition-all"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-primary hover:brand-gradient hover:text-white transition-all"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-primary hover:brand-gradient hover:text-white transition-all"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-primary uppercase tracking-[4px] mb-8">Navigation</h4>
                    <ul class="space-y-4 text-sm font-bold uppercase tracking-tighter text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-all">Top Courses</a></li>
                        <li><a href="#" class="hover:text-primary transition-all">Join as Mentor</a></li>
                        <li><a href="#" class="hover:text-primary transition-all">Referral Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-primary uppercase tracking-[4px] mb-8">Legal</h4>
                    <ul class="space-y-4 text-sm font-bold uppercase tracking-tighter text-gray-400">
                        <li><a href="#" class="hover:text-primary transition-all">Terms of Use</a></li>
                        <li><a href="#" class="hover:text-primary transition-all">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary transition-all">Refund Terms</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-12 border-t border-orange-50 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-[10px] font-black uppercase tracking-[3px] text-gray-400">© 2026 Skills Pehle Ecosystem. Global Inc.</p>
                <div class="flex gap-8 text-[10px] font-black uppercase tracking-[3px] text-gray-400">
                    <span>GSTIN: 09XXXXXXXXXXXX</span>
                    <span>PATNA, BIHAR</span>
                </div>
            </div>
        </div>
    </footer>

    <button id="scrollTop" class="fixed bottom-10 right-10 w-14 h-14 brand-gradient text-white rounded-2xl shadow-2xl hidden items-center justify-center transition-all z-[200] hover:scale-110 active:scale-95">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script>
        $(document).ready(function() {
            // Smooth Reveal for all sections
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('animate__animated animate__fadeInUp');
                        entry.target.style.opacity = 1;
                    }
                });
            }, { threshold: 0.1 });

            $('section, .grid > div').each(function() {
                this.style.opacity = 0;
                observer.observe(this);
            });

            // Scroll Top Visibility
            $(window).scroll(function() {
                if ($(this).scrollTop() > 500) {
                    $('#scrollTop').fadeIn().css('display', 'flex');
                } else {
                    $('#scrollTop').fadeOut();
                }
            });

            $('#scrollTop').click(function() {
                window.scrollTo({top: 0, behavior: 'smooth'});
            });
        });
    </script>
</body>
</html>
