<div class="overflow-x-auto bg-white border border-slate-200 shadow-sm rounded-[2rem]">
    <table class="w-full text-left border-collapse min-w-[900px]">
        <thead class="bg-slate-50/80 border-b border-slate-200">
            <tr>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Bundle Details</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Included Courses
                </th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Package
                    Price</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Status
                </th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($bundles as $bundle)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <p class="text-sm font-black text-slate-800 group-hover:text-[#0777be] transition-colors">
                            {{ $bundle->title }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">ID:
                            #BNDL-{{ $bundle->id }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5 max-w-xs">
                            @foreach ($bundle->courses as $course)
                                <span
                                    class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-md text-[9px] font-black border border-indigo-100">
                                    {{ $course->title }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-black text-green-600">₹{{ number_format($bundle->price, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        {{-- Live Status Logic --}}
                        @if ($bundle->is_published)
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black bg-green-50 text-green-600 border border-green-100 uppercase">Live</span>
                        @else
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-400 border border-slate-200 uppercase">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            {{-- Edit Button: Ab ye redirect karega dedicated page par --}}
                            <a href="{{ route('admin.bundles.edit', $bundle->id) }}"
                                class="p-2 text-slate-400 hover:text-[#0777be] hover:bg-blue-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            {{-- Delete Button: Iska logic confirmBundleDelete function handle karega --}}
                            <button type="button"
                                onclick="confirmBundleDelete({{ $bundle->id }}, '{{ addslashes($bundle->title) }}')"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            <form id="bundle-delete-form-{{ $bundle->id }}"
                                action="{{ route('admin.bundles.delete', $bundle->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center text-slate-400 italic">No bundles found. Use the
                        button above to create one.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
