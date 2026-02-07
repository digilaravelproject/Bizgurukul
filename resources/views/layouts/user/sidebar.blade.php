 <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-navy border-r border-primary/10 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col shadow-xl md:shadow-none">

     <div class="relative z-10 flex flex-col h-full bg-customWhite">
         {{-- Sidebar Logo --}}
         <div class="p-6 flex justify-between items-center h-20 border-b border-navy">
             <div class="flex items-center space-x-3">
                 <div class="w-auto h-9 flex items-center justify-center">
                    <img src="{{ asset('storage/site_images/logo1.png') }}"
                            alt="Logo" class="h-full w-auto object-contain group-hover:scale-110 transition-transform" loading="lazy">
                 </div>
             </div>
             <button @click="sidebarOpen = false" class="md:hidden text-mutedText hover:text-primary transition p-2">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                     </path>
                 </svg>
             </button>
         </div>

         {{-- Sidebar Nav Menu --}}
         <nav class="flex-1 p-4 space-y-1 overflow-y-auto no-scrollbar">
             <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Learning Hub</p>

             {{-- Dashboard --}}
             <a href="{{ route('dashboard') }}"
                 class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                 <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                     </path>
                 </svg>
                 <span class="text-sm font-bold">Dashboard</span>
             </a>

             {{-- My Courses Link --}}
             <a href="{{ route('student.courses.index') }}"
                 class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.courses.*') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">

                 <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                     </path>
                 </svg>

                 <span class="text-sm font-bold">My Courses</span>
             </a>

             <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mt-6 mb-2">Affiliate
                 Section</p>

             {{-- My Referrals --}}
             <a href="#"
                 class="flex items-center px-4 py-3 rounded-2xl text-mutedText hover:bg-navy hover:text-primary transition group">
                 <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                     </path>
                 </svg>
                 <span class="text-sm font-bold">My Referrals</span>
             </a>

             {{-- Earnings --}}
             <a href="#"
                 class="flex items-center px-4 py-3 rounded-2xl text-mutedText hover:bg-navy hover:text-primary transition group">
                 <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                     </path>
                 </svg>
                 <span class="text-sm font-bold">Earnings</span>
             </a>
         </nav>

         {{-- Sidebar User Footer --}}
         <div class="p-4 mt-auto border-t border-navy bg-navy/30">
            <div class="flex items-center p-2 rounded-xl border border-primary/5 bg-customWhite">
                <div class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold text-xs shadow-md">
                    {{ substr(Auth::user()->name ?? 'ST', 0, 2) }}
                </div>
                <div class="ml-3 flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-mainText truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] text-primary font-bold uppercase tracking-wider">Verified Student</p>
                </div>

             </div>
         </div>
     </div>
 </aside>

