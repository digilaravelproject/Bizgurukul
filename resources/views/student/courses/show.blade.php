@extends('layouts.user.app')

@section('content')
    <div class="space-y-10">
        {{-- 1. Course Header Section --}}
        <div class="flex flex-col lg:flex-row gap-10 items-start">
            <div class="flex-1 space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest italic">Premium Content</span>
                </div>
                <h1 class="text-4xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">
                    {{ $course->title }}
                </h1>
                <p class="text-slate-400 text-sm font-medium italic leading-relaxed max-w-2xl">
                    {{ $course->description }}
                </p>
            </div>

            <div
                class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl flex flex-col items-center gap-4 min-w-[280px]">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">One-Time Enrollment</p>
                <p class="text-4xl font-black text-slate-900 italic tracking-tighter">
                    ₹{{ number_format($course->price, 2) }}
                </p>

                {{-- Payment Button Logic --}}
                @if (Auth::check() && $course->isPurchasedBy(Auth::id()))
                    <button
                        class="w-full bg-emerald-500 text-white py-4 rounded-2xl text-[11px] font-black uppercase italic tracking-widest shadow-lg shadow-emerald-200 cursor-default"
                        disabled>
                        Already Enrolled
                    </button>
                @else
                    <button id="pay-button"
                        class="w-full bg-indigo-600 text-white py-4 rounded-2xl text-[11px] font-black uppercase italic tracking-widest shadow-lg shadow-indigo-200 active:scale-95 transition-all">
                        Buy This Course
                    </button>
                @endif
            </div>
        </div>

        {{-- 2. Course Content & Curriculum --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div
                    class="relative aspect-video bg-black rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white group">
                    <video src="{{ $course->demo_video_url }}" class="w-full h-full object-contain" controls
                        controlsList="nodownload" poster="{{ $course->thumbnail }}">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-black text-slate-800 uppercase italic mb-6 tracking-tight">What you'll learn
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (explode("\n", $course->short_description) as $point)
                            @if (trim($point))
                                <div class="flex items-center gap-3 text-slate-500 italic text-xs font-bold">
                                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                    </svg>
                                    {{ trim($point) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Lesson Sidebar --}}
            <div class="space-y-6">
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl">
                    <h3 class="text-sm font-black uppercase italic tracking-widest mb-6 border-b border-slate-800 pb-4">
                        Course Curriculum
                    </h3>
                    <div class="space-y-4">
                        @forelse($course->lessons as $index => $lesson)
                            <div
                                class="group flex items-center justify-between p-4 bg-slate-800 rounded-2xl hover:bg-indigo-600 transition-all cursor-not-allowed opacity-70">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-[10px] font-black text-slate-500 group-hover:text-white italic">0{{ $index + 1 }}</span>
                                    <span
                                        class="text-[11px] font-black uppercase italic tracking-tight">{{ $lesson->title }}</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-600 group-hover:text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        @empty
                            <p class="text-xs italic text-slate-500 text-center py-4">Syllabus being updated...</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Razorpay Script & Logic --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.onclick = function(e) {
                payButton.innerText = "Processing...";
                payButton.disabled = true;

                // 1. Create Order
                fetch("{{ route('razorpay.create', $course->id) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            payButton.innerText = "Buy This Course";
                            payButton.disabled = false;
                            return;
                        }

                        var options = {
                            "key": data.key,
                            "amount": data.amount,
                            "currency": "INR",
                            "name": "Course Payment",
                            "description": data.course_name,
                            "order_id": data.order_id,
                            "handler": function(response) {
                                // 2. Verify Payment
                                fetch("{{ route('razorpay.verify') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify(response)
                                    })
                                    .then(res => res.json())
                                    .then(verification => {
                                        if (verification.status === 'success') {
                                            alert('Welcome! Payment Successful.');
                                            window.location.reload();
                                        } else {
                                            alert('Verification Failed');
                                        }
                                    });
                            },
                            "modal": {
                                "ondismiss": function() {
                                    payButton.innerText = "Buy This Course";
                                    payButton.disabled = false;
                                }
                            },
                            "prefill": {
                                "name": "{{ Auth::user()->name ?? '' }}",
                                "email": "{{ Auth::user()->email ?? '' }}"
                            },
                            "theme": {
                                "color": "#4F46E5"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    })
                    .catch(err => {
                        console.error(err);
                        payButton.innerText = "Buy This Course";
                        payButton.disabled = false;
                    });
            }
        }
    </script>
@endsection
