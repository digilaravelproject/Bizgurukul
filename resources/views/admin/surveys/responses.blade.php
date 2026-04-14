@extends('layouts.admin')
@section('title', 'Survey Responses')

@section('content')
    <div class="container-fluid font-sans antialiased text-mainText">

        <div class="mb-8 animate-fade-in">
            <h2 class="text-2xl font-black tracking-tight">Survey Responses</h2>
            <p class="text-sm text-mutedText mt-1 font-medium">View detailed feedback from your students.</p>
        </div>

        <div class="bg-white rounded-[2rem] border border-primary/10 shadow-xl overflow-hidden animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                        <tr>
                            <th class="px-8 py-6">Student</th>
                            <th class="px-6 py-6">Question</th>
                            <th class="px-6 py-6">Response</th>
                            <th class="px-8 py-6 text-right">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($responses as $res)
                            <tr class="hover:bg-primary/[0.02] transition-colors">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-mainText">{{ $res->user->name }}</div>
                                    <div class="text-[10px] text-mutedText mt-0.5">{{ $res->user->email }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-xs font-semibold text-mutedText truncate max-w-xs">
                                        {{ $res->question->question_text }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($res->question->question_type === 'options')
                                        <span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-black tracking-wider uppercase">
                                            {{ $res->option->option_text ?? 'N/A' }}
                                        </span>
                                    @else
                                        <div class="text-xs text-mainText font-medium bg-navy px-3 py-2 rounded-xl inline-block">
                                            {{ $res->response_text }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-[10px] font-black text-mutedText uppercase tracking-widest">
                                        {{ $res->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-mutedText/30">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-sm font-bold italic">No responses received yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($responses->hasPages())
                <div class="px-8 py-6 bg-primary/5 border-t border-primary/5">
                    {{ $responses->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
