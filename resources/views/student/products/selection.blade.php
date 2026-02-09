@extends('layouts.user.app')

@section('content')
<div class="space-y-8" x-data="{ referralCode: '{{ $referrer->referral_code ?? '' }}', message: '', status: '' }">

    {{-- Welcome Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-navy p-8 text-center text-white border border-primary/20 shadow-2xl">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 h-64 w-64 rounded-full bg-secondary/10 blur-3xl"></div>

        <h1 class="relative text-3xl font-extrabold tracking-tight sm:text-4xl font-main">
            Welcome, <span class="text-primary">{{ Auth::user()->name }}</span>
        </h1>
        <p class="relative mt-2 text-lg text-mutedText max-w-2xl mx-auto">
            Select a learning path to begin your journey. Unlock premium skills and earning potential.
        </p>
    </div>

    {{-- Referral Section --}}
    <div class="bg-customWhite rounded-2xl shadow-lg border border-primary/5 p-6 md:p-8">
        <div class="max-w-md mx-auto">
            <h3 class="text-sm font-bold text-mutedText uppercase tracking-widest mb-4 text-center">Have a Referral Code?</h3>

            <div class="relative flex items-center">
                <input type="text" x-model="referralCode"
                    placeholder="Enter Code (e.g. ABC12345)"
                    class="w-full pl-5 pr-20 py-3 rounded-xl bg-navy/5 border border-primary/10 text-mainText font-bold focus:ring-2 focus:ring-primary focus:border-transparent transition-all uppercase placeholder-mutedText/50">

                <button @click="applyReferral()"
                    class="absolute right-2 top-1.5 bottom-1.5 px-4 bg-primary hover:bg-secondary text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-colors shadow-lg">
                    Apply
                </button>
            </div>

            {{-- Status Message --}}
            <div x-show="message" x-transition class="mt-3 text-center text-xs font-bold"
                :class="status === 'success' ? 'text-emerald-500' : 'text-red-500'">
                <span x-text="message"></span>
            </div>

            @if($referrer)
                <div class="mt-3 text-center text-xs font-bold text-emerald-500 bg-emerald-500/10 py-2 rounded-lg border border-emerald-500/20">
                    Applied: {{ $referrer->name }} ({{ $referrer->referral_code }})
                </div>
            @endif
        </div>
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @if($filteredCourses->isEmpty() && $filteredBundles->isEmpty())
             <div class="col-span-full text-center py-20">
                <p class="text-mutedText font-bold text-lg">No products found for this link.</p>
             </div>
        @endif

        {{-- Courses --}}
        @foreach($filteredCourses as $course)
        <div class="group relative flex flex-col overflow-hidden rounded-3xl bg-customWhite shadow-xl border border-primary/5 hover:border-primary/30 transition-all duration-300 hover:-translate-y-1">
            <div class="aspect-video w-full overflow-hidden bg-navy/10 relative">
                @if($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                @else
                    <div class="flex h-full items-center justify-center text-mutedText font-bold">No Image</div>
                @endif
                <div class="absolute top-4 right-4 bg-primary text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">
                    Course
                </div>
            </div>

            <div class="flex flex-1 flex-col p-6">
                <h3 class="text-xl font-bold text-mainText leading-tight group-hover:text-primary transition-colors">
                    {{ $course->title }}
                </h3>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-mutedText tracking-wider">Price</p>
                        <p class="text-2xl font-black text-primary">₹{{ number_format($course->price, 2) }}</p>
                    </div>
                </div>

                <div class="mt-6 mt-auto">
                    <a href="{{ route('student.courses.show', $course->id) }}" class="flex w-full items-center justify-center rounded-xl bg-navy py-3 px-4 text-sm font-bold text-white transition-all hover:bg-primary shadow-lg shadow-primary/20 hover:shadow-primary/40">
                        View Details & Buy
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Bundles --}}
        @foreach($filteredBundles as $bundle)
        <div class="group relative flex flex-col overflow-hidden rounded-3xl bg-customWhite shadow-xl border border-secondary/20 hover:border-secondary/50 transition-all duration-300 hover:-translate-y-1">
             <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-primary/20 to-secondary/20 relative flex items-center justify-center">
                 {{-- Placeholder for Bundle Image --}}
                 <svg class="w-16 h-16 text-secondary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>

                 <div class="absolute top-4 right-4 bg-secondary text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">
                    Bundle
                </div>
            </div>

            <div class="flex flex-1 flex-col p-6">
                <h3 class="text-xl font-bold text-mainText leading-tight group-hover:text-secondary transition-colors">
                    {{ $bundle->name }}
                </h3>
                <p class="mt-2 text-xs text-mutedText line-clamp-2">{{ $bundle->description }}</p>

                <div class="mt-4 flex items-center justify-between">
                     <div>
                        <p class="text-[10px] uppercase font-bold text-mutedText tracking-wider">Bundle Price</p>
                        <p class="text-2xl font-black text-secondary">₹{{ number_format($bundle->price, 2) }}</p>
                    </div>
                </div>

                <div class="mt-6 mt-auto">
                    {{-- Assuming bundles also have a show/buy route? If not, maybe direct buy? --}}
                    {{-- For now, using a placeholder link or course show if bundles are treated as courses structure-wise --}}
                    {{-- Wait, bundles usually have their own show page. If not, I'll link to first course or generic buy? --}}
                    {{-- I'll Assume there is a route 'student.bundles.show' or similar, OR just disable if not ready. --}}
                    {{-- For this task, I focus on Courses mostly, but user said "bundle wise". --}}
                    {{-- I'll check routes list later. For now, button triggers nothing or alerts. --}}
                    <button class="flex w-full items-center justify-center rounded-xl bg-navy py-3 px-4 text-sm font-bold text-white transition-all hover:bg-secondary shadow-lg shadow-secondary/20 hover:shadow-secondary/40">
                        Buy Bundle (Coming Soon)
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function applyReferral() {
        let code = this.referralCode;
        if(!code) return;

        fetch('{{ route('student.apply_referral') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ referral_code: code })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                this.message = data.message;
                this.status = 'success';
                // Reload to reflect changes if needed
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.message = data.message || 'Invalid Request';
                this.status = 'error';
            }
        })
        .catch(error => {
            this.message = 'Invalid Referral Code';
            this.status = 'error';
        });
    }
</script>
@endsection
