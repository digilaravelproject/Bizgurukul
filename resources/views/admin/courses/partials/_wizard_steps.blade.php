<div class="mb-10">
    <div class="flex items-center justify-between relative px-4 md:px-10">
        {{-- Progress Line --}}
        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-primary/5 -z-10 rounded-full mx-4 md:mx-10">
        </div>

        @php
            $steps = [
                [
                    'key' => 'basic',
                    'label' => 'Basic Info',
                    'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'key' => 'lessons',
                    'label' => 'Curriculum',
                    'icon' =>
                        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                ],
                [
                    'key' => 'resources',
                    'label' => 'Resources',
                    'icon' =>
                        'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                ],
                [
                    'key' => 'settings',
                    'label' => 'Publish',
                    'icon' =>
                        'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                ],
            ];
        @endphp

        @foreach ($steps as $step)
            @php
                $isActive = $activeTab === $step['key'];
                // ID parameter fix applied below
                $url =
                    isset($course) && $course->id
                        ? route('admin.courses.edit', ['id' => $course->id, 'tab' => $step['key']])
                        : '#';
                $disabled = !isset($course) && $step['key'] != 'basic' ? 'pointer-events-none opacity-50' : '';
            @endphp

            <a href="{{ $url }}" class="relative flex flex-col items-center group {{ $disabled }}">

                {{-- Icon Circle --}}
                <div
                    class="h-10 w-10 md:h-14 md:w-14 rounded-full flex items-center justify-center transition-all duration-300 border-4 z-10
                    {{ $isActive ? 'bg-primary border-primary/20 text-white shadow-xl shadow-primary/30 scale-110' : 'bg-surface border-primary/10 text-mutedText hover:border-primary/40' }}">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}" />
                    </svg>
                </div>

                {{-- Label --}}
                <span
                    class="absolute top-16 w-32 text-center text-[10px] md:text-xs font-black uppercase tracking-wider transition-colors {{ $isActive ? 'text-primary' : 'text-mutedText' }}">
                    {{ $step['label'] }}
                </span>
            </a>
        @endforeach
    </div>
</div>
