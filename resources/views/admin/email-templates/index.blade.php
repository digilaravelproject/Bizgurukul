@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-start justify-between flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Email Templates</h2>
        <p class="text-sm text-mutedText font-medium mt-1">Manage and customise all outgoing email templates for your platform.</p>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-3.5 rounded-xl text-sm font-bold">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($templates as $template)
    @php
        $icons = [
            'welcome'                 => ['icon' => 'fa-star',              'color' => 'text-yellow-400',  'bg' => 'bg-yellow-400/10'],
            'lead_converted'          => ['icon' => 'fa-user-plus',         'color' => 'text-blue-400',    'bg' => 'bg-blue-400/10'],
            'course_purchased'        => ['icon' => 'fa-graduation-cap',    'color' => 'text-green-400',   'bg' => 'bg-green-400/10'],
            'plan_upgraded'           => ['icon' => 'fa-rocket',            'color' => 'text-purple-400',  'bg' => 'bg-purple-400/10'],
            'reset_password'          => ['icon' => 'fa-lock',              'color' => 'text-red-400',     'bg' => 'bg-red-400/10'],
            'forgot_password'         => ['icon' => 'fa-key',               'color' => 'text-orange-400',  'bg' => 'bg-orange-400/10'],
            'coupon_purchased'        => ['icon' => 'fa-ticket-alt',        'color' => 'text-amber-400',   'bg' => 'bg-amber-400/10'],
            'coupon_transfer_sender'  => ['icon' => 'fa-share',             'color' => 'text-cyan-400',    'bg' => 'bg-cyan-400/10'],
            'coupon_transfer_receiver'=> ['icon' => 'fa-gift',              'color' => 'text-pink-400',    'bg' => 'bg-pink-400/10'],
            'withdrawal_requested'    => ['icon' => 'fa-money-bill-wave',   'color' => 'text-yellow-400',  'bg' => 'bg-yellow-400/10'],
            'withdrawal_approved'     => ['icon' => 'fa-check-double',      'color' => 'text-green-400',   'bg' => 'bg-green-400/10'],
            'admin_notification'      => ['icon' => 'fa-bell',              'color' => 'text-indigo-400',  'bg' => 'bg-indigo-400/10'],
        ];
        $meta = $icons[$template->key] ?? ['icon' => 'fa-envelope', 'color' => 'text-primary', 'bg' => 'bg-primary/10'];
    @endphp

    <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-5 flex flex-col gap-4 hover:border-primary/30 transition-all group">
        {{-- Header --}}
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 {{ $meta['bg'] }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fas {{ $meta['icon'] }} {{ $meta['color'] }} text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-black text-mainText truncate">{{ $template->name }}</h3>
                <p class="text-xs text-mutedText font-medium mt-0.5 font-mono">{{ $template->key }}</p>
            </div>
        </div>

        {{-- Subject Preview --}}
        <div class="bg-navy rounded-xl px-4 py-2.5 border border-primary/5">
            <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-1">Subject</p>
            <p class="text-xs text-mainText font-medium truncate">{{ $template->subject }}</p>
        </div>

        {{-- Variables --}}
        @if($template->variables && count($template->variables) > 0)
        <div class="flex flex-wrap gap-1.5">
            @foreach($template->variables as $var)
                <span class="text-[10px] bg-primary/10 text-primary font-mono font-bold px-2 py-0.5 rounded-md">{{ '{' . '{' }} {{ $var }} {{ '}' . '}' }}</span>
            @endforeach
        </div>
        @endif

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-2 border-t border-primary/5 mt-auto">
            <span class="text-[11px] text-mutedText">Updated {{ $template->updated_at->diffForHumans() }}</span>
            <a href="{{ route('admin.email-templates.edit', $template->key) }}"
               class="flex items-center gap-2 text-xs font-bold text-primary hover:text-white bg-primary/10 hover:bg-primary px-4 py-2 rounded-xl transition-all">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection
