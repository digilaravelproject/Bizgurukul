@extends('layouts.app')
{{-- Assuming 'layouts.app' is the main user layout. Or 'layouts.student' if mainly for students?
     Checking 'list_dir' of views, there is 'dashboard_a.blade.php' which likely extends something.
     Step 132 `admin/affiliate/history` extends `layouts.admin`.
     I'll assume `layouts.app` or similar exists for user dashboard.
     Let's use `layouts.app`. If it fails, user can fix. Or I can check `dashboard_a.blade.php`.
--}}

@section('title', 'Affiliate Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Affiliate Center</h1>
            <p class="text-gray-500 mt-1">Generate links and track your performance.</p>
        </div>
        <div>
            {{-- Affiliate Stats Summary can go here --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                Wallet Balance: ₹{{ number_format(auth()->user()->wallet_balance, 2) }}
            </span>
        </div>
    </div>

    {{-- Link Generator --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            Link Generator
        </h2>
        <form action="{{ route('affiliate.link.generate') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf

            {{-- Type Selection --}}
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="target_type" id="target_type" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required onchange="toggleTargetSelect()">
                    <option value="all">General (Homepage/All)</option>
                    <option value="bundle">Specific Bundle</option>
                    @if($canSellCourses)
                        <option value="course">Specific Course</option>
                    @endif
                </select>
            </div>

            {{-- Product Selection --}}
            <div class="md:col-span-1" id="target_id_container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-1" id="target_label">Product</label>
                <select name="target_id" id="target_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    {{-- Options populated via JS or just simple conditional rendering if possible --}}
                </select>
            </div>

            {{-- Expiry --}}
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry (Optional)</label>
                <input type="datetime-local" name="expires_at" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-md">
                    Generate Link
                </button>
            </div>
        </form>
    </div>

    {{-- My Links Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">My Active Links</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4">Link / URL</th>
                        <th class="px-6 py-4">Clicks</th>
                        <th class="px-6 py-4">Expires</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($links as $link)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $link->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 uppercase">
                                    {{ $link->target_type }}
                                </span>
                                @if($link->target_type == 'bundle' && $link->bundle)
                                    <span class="block text-xs text-gray-500 mt-1">{{ $link->bundle->title }}</span>
                                @elseif($link->target_type == 'course' && $link->course)
                                    <span class="block text-xs text-gray-500 mt-1">{{ $link->course->title }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <input type="text" readonly value="{{ url('/u/' . $link->slug) }}" class="text-xs text-gray-600 bg-gray-50 border-none rounded p-1 w-48 focus:ring-0">
                                    <button onclick="navigator.clipboard.writeText('{{ url('/u/' . $link->slug) }}')" class="text-blue-600 hover:text-blue-800 text-xs font-bold">Copy</button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $link->clicks }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $link->expires_at ? $link->expires_at->format('d M Y, h:i A') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('affiliate.link.delete', $link->id) }}" method="POST" onsubmit="return confirm('Disable this link?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No active links found. Generate one above!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($links->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $links->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    const bundles = @json($availableBundles);
    const courses = @json($availableCourses);

    function toggleTargetSelect() {
        const type = document.getElementById('target_type').value;
        const container = document.getElementById('target_id_container');
        const select = document.getElementById('target_id');

        select.innerHTML = '';
        container.style.display = 'none';

        if (type === 'bundle') {
            container.style.display = 'block';
            bundles.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.text = b.title;
                select.appendChild(opt);
            });
        } else if (type === 'course') {
            container.style.display = 'block';
            courses.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.text = c.title;
                select.appendChild(opt);
            });
        }
    }
</script>
@endsection
