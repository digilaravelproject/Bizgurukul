@extends('web.layouts.app')

@section('title', config('app.name', 'Skills Pehle') . ' | The Future of Digital Learning')

@section('content')
<div class="relative overflow-hidden bg-white">
    <!-- Hero Section: Dynamic & Graphic Heavy -->
    <section class="relative pt-20 pb-32 lg:pt-32 lg:pb-48 overflow-hidden">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
            <div class="absolute top-10 right-10 w-96 h-96 bg-primary/10 rounded-full blur-3xl mix-blend-multiply"></div>
            <div class="absolute bottom-10 left-10 w-72 h-72 bg-secondary/10 rounded-full blur-3xl mix-blend-multiply"></div>
            <svg class="absolute right-0 bottom-0 text-primary/5 w-1/2 h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <polygon points="0,100 100,0 100,100" fill="currentColor" />
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="text-left animate-fade-in-down">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary font-bold text-sm tracking-wide mb-6">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        NEW COURSES AVAILABLE
                    </div>
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight text-mainText mb-6 leading-tight">
                        Ignite Your <span class="text-transparent bg-clip-text brand-gradient">Digital</span> Journey
                    </h1>
                    <p class="text-lg md:text-xl text-mutedText mb-8 max-w-lg leading-relaxed">
                        The ultimate platform for modern skills. Get certified, get hired, and build the life you've always wanted.
                    </p>

                    <div class="flex items-center gap-6 mb-12">
                        <div class="flex -space-x-4">
                            <img class="w-12 h-12 rounded-full border-2 border-white relative z-30" src="https://ui-avatars.com/api/?name=A+B&background=random" alt="Student">
                            <img class="w-12 h-12 rounded-full border-2 border-white relative z-20" src="https://ui-avatars.com/api/?name=C+D&background=random" alt="Student">
                            <img class="w-12 h-12 rounded-full border-2 border-white relative z-10" src="https://ui-avatars.com/api/?name=E+F&background=random" alt="Student">
                            <div class="w-12 h-12 rounded-full border-2 border-white relative z-0 bg-gray-100 flex items-center justify-center text-xs font-bold text-mainText">
                                +5k
                            </div>
                        </div>
                        <div class="text-sm font-medium text-mutedText">
                            <span class="text-mainText font-bold block">5000+ Students</span>
                            Already Learning
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#featured" class="px-8 py-4 rounded-full bg-primary text-white font-medium hover:bg-secondary hover:shadow-lg hover:shadow-primary/30 transition-all text-center">
                            Start Learning Now
                        </a>
                        <a href="#stats" class="px-8 py-4 rounded-full bg-white text-mainText font-medium border-2 border-gray-100 hover:border-gray-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                            Watch Video
                        </a>
                    </div>
                </div>

                <!-- Graphic/Image Content -->
                <div class="relative lg:ml-auto">
                    <div class="relative rounded-3xl overflow-hidden glass-panel shadow-2xl z-10 p-2">
                        <div class="bg-gray-100 rounded-2xl overflow-hidden aspect-[4/3] relative">
                            <!-- Placeholder for a real dashboard/system image -->
                            <img src="https://source.unsplash.com/random/800x600/?technology,learning" alt="Platform Overview" class="w-full h-full object-cover">

                            <!-- Floating Badge 1 -->
                            <div class="absolute top-8 -left-6 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 animate-bounce" style="animation-duration: 3s;">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-mutedText font-medium">Certification</div>
                                        <div class="text-sm font-bold text-mainText">Approved</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Badge 2 -->
                            <div class="absolute bottom-8 -right-6 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 animate-bounce" style="animation-duration: 4s; animation-delay: 1s;">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-mutedText font-medium">Growth Rates</div>
                                        <div class="text-sm font-bold text-mainText">+125%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-16 bg-navy border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-200">
                <div class="px-4">
                    <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text brand-gradient mb-2">50+</div>
                    <div class="text-sm font-bold text-mutedText uppercase tracking-widest">Masterclasses</div>
                </div>
                <div class="px-4">
                    <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text brand-gradient mb-2">12k</div>
                    <div class="text-sm font-bold text-mutedText uppercase tracking-widest">Active Users</div>
                </div>
                <div class="px-4">
                    <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text brand-gradient mb-2">99%</div>
                    <div class="text-sm font-bold text-mutedText uppercase tracking-widest">Success Rate</div>
                </div>
                <div class="px-4">
                    <div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text brand-gradient mb-2">24/7</div>
                    <div class="text-sm font-bold text-mutedText uppercase tracking-widest">Expert Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses Grid -->
    <section id="featured" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-mainText mb-4">Our Top Rated Programs</h2>
                <p class="text-lg text-mutedText">High-impact learning experiences designed by industry veterans.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Fallback / Demo Items if $courses empty -->
                @forelse(isset($courses) ? $courses->take(6) : [] as $course)
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow border border-gray-100 flex flex-col h-full group">
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ Str::contains($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
                                <span class="px-3 py-1 rounded bg-white text-mainText text-xs font-bold uppercase">{{ $course->category->name ?? 'Course' }}</span>
                                <span class="flex items-center gap-1 text-white text-sm font-bold">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    4.9
                                </span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-mainText mb-2 group-hover:text-primary transition-colors line-clamp-2">{{ $course->title }}</h3>
                            <p class="text-mutedText text-sm mb-4 line-clamp-2 flex-grow">{!! strip_tags($course->description) !!}</p>

                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                <span class="text-2xl font-black text-transparent bg-clip-text brand-gradient">₹{{ number_format($course->price) }}</span>
                                <a href="{{ route('course.show', $course->id) }}" class="text-primary font-bold hover:text-secondary group-hover:underline">Join Now &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Placeholder data if no courses available -->
                    @for($i=1; $i<=3; $i++)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow border border-gray-100 flex flex-col h-full group">
                            <div class="relative h-56 overflow-hidden bg-gray-200">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent z-10"></div>
                                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end z-20">
                                    <span class="px-3 py-1 rounded bg-white text-mainText text-xs font-bold uppercase">Marketing</span>
                                </div>
                            </div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-xl font-bold text-mainText mb-2 group-hover:text-primary transition-colors">Advanced Meta Ads Strategies</h3>
                                <p class="text-mutedText text-sm mb-4 flex-grow">A complete guide to scaling businesses utilizing Facebook and Instagram ads.</p>
                                <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                    <span class="text-2xl font-black text-transparent bg-clip-text brand-gradient">₹3,499</span>
                                    <a href="#" class="text-primary font-bold hover:text-secondary hover:underline">Join Now &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endforelse
            </div>

            <div class="mt-16 text-center">
                <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full border-2 border-primary text-primary font-bold hover:bg-primary hover:text-white transition-all">
                    Browse 40+ More Courses
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features / Why Choose Us -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-mainText mb-6">Learn faster. Build better. <br/>Grow exponentially.</h2>
                    <p class="text-lg text-mutedText mb-8">
                        We don't just provide videos. We provide a comprehensive ecosystem designed for your successful career transition.
                    </p>

                    <div class="space-y-6">
                        <!-- Feature 1 -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-mainText mb-2">Industry-Vetted Content</h3>
                                <p class="text-mutedText">Courses built by top practitioners with decades of combined experience.</p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0 text-secondary">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-mainText mb-2">Lifetime Access</h3>
                                <p class="text-mutedText">Learn at your own pace. All updates provided completely free of charge.</p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-mainText mb-2">Verifiable Certificates</h3>
                                <p class="text-mutedText">Stand out to employers with unique, shareable course completion certificates.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <img src="https://source.unsplash.com/random/600x800/?developer,designer" alt="Working Professional" class="rounded-3xl shadow-2xl object-cover h-[600px] w-full">
                    <!-- Decor overlay -->
                    <div class="absolute -bottom-8 -left-8 w-48 h-48 bg-primary rounded-full mix-blend-multiply opacity-20 blur-2xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials / Social Proof -->
    <section class="py-24 bg-navy">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-mainText mb-4">Don't just take our word for it</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex gap-1 text-yellow-400 mb-6">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg>
                        @endfor
                    </div>
                    <p class="text-mutedText mb-8 italic">"Skills Pehle entirely changed my trajectory. I went from struggling to find entry-level jobs to landing a senior developer role within 6 months of completing the Master bundle."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=R+S&background=random" alt="Rahul" class="w-12 h-12 rounded-full">
                        <div>
                            <div class="font-bold text-mainText">Rahul Sharma</div>
                            <div class="text-sm text-primary">Senior Frontend Dev</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex gap-1 text-yellow-400 mb-6">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg>
                        @endfor
                    </div>
                    <p class="text-mutedText mb-8 italic">"The instructors don't just teach theory; they show you how to apply it in the real world. The support community is also incredibly helpful."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=P+M&background=random" alt="Priya" class="w-12 h-12 rounded-full">
                        <div>
                            <div class="font-bold text-mainText">Priya M.</div>
                            <div class="text-sm text-primary">Digital Marketer</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex gap-1 text-yellow-400 mb-6">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg>
                        @endfor
                    </div>
                    <p class="text-mutedText mb-8 italic">"Best investment I've made in myself. The ROI on these courses is insane compared to traditional degrees or bootcamps."</p>
                    <div class="flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name=A+K&background=random" alt="Arjun" class="w-12 h-12 rounded-full">
                        <div>
                            <div class="font-bold text-mainText">Arjun K.</div>
                            <div class="text-sm text-primary">UI/UX Designer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-20 relative">
        <div class="absolute inset-0 bg-primary z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary z-0 opacity-90"></div>

        <div class="max-w-4xl mx-auto px-4 text-center relative z-10 text-white">
            <h2 class="text-3xl md:text-5xl font-bold mb-6 text-white text-shadow">Stop waiting. Start doing.</h2>
            <p class="text-lg text-white/90 mb-10 max-w-2xl mx-auto">Get absolute access to all tools, templates, and courses.</p>
            <a href="{{ route('register') }}" class="inline-block px-10 py-4 rounded-full bg-white text-primary font-bold hover:bg-gray-50 transition-all shadow-xl hover:-translate-y-1 text-lg">
                Join Skills Pehle Today
            </a>
        </div>
    </section>
</div>
@endsection
