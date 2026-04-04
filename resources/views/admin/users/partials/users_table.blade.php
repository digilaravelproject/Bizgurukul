@forelse($users as $user)
    <tr class="hover:bg-primary/[0.02] transition-colors group">
        <td class="px-8 py-5">
            <div class="flex items-center gap-4">
                <div class="relative h-12 w-12 flex-shrink-0">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}"
                             class="h-full w-full rounded-2xl object-cover ring-2 ring-primary/5 shadow-sm">
                    @else
                        <div class="h-full w-full rounded-2xl brand-gradient flex items-center justify-center text-white font-black text-sm shadow-sm border border-white">
                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="font-bold text-mainText text-sm truncate max-w-[180px]">
                        {{ $user->name }}
                    </div>
                    <div class="text-xs text-mutedText truncate max-w-[180px]">
                        <span>{{ $user->email }}</span>
                        @if($user->state)
                            <span class="text-primary/60 font-black ml-1">• {{ $user->state->name }}</span>
                        @endif
                    </div>
                    <div class="text-[11px] font-bold text-mainText/70 mt-0.5">{{ $user->mobile ?: 'No Mobile' }}</div>
                    <div class="mt-1 text-[9px] font-black text-primary/80 uppercase">REF: {{ $user->referral_code }}</div>
                </div>
            </div>
        </td>
        
        <td class="px-6 py-5">
            @if($user->referrer)
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-mainText">{{ $user->referrer->name }}</span>
                    <span class="text-[10px] font-black text-primary/70 uppercase">{{ $user->referrer->referral_code }}</span>
                </div>
            @else
                <span class="text-[10px] font-bold text-mutedText/40 italic">No Sponsor</span>
            @endif
        </td>

        <td class="px-6 py-5">
            @foreach($user->roles as $role)
                <span class="inline-flex items-center rounded-lg bg-navy px-3 py-1 text-[10px] font-black text-mainText border border-primary/5">
                    {{ $role->name }}
                </span>
            @endforeach
        </td>

        <td class="px-6 py-5 text-center">
            @php
                $kycClass = [
                    'verified' => 'bg-green-50 text-green-600 border-green-200',
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'rejected' => 'bg-red-50 text-red-600 border-red-200',
                    'not_submitted' => 'bg-navy text-mutedText border-primary/5'
                ][$user->kyc_status] ?? 'bg-navy text-mutedText border-primary/5';
            @endphp
            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border shadow-sm {{ $kycClass }}">
                {{ str_replace('not_submitted', 'NONE', $user->kyc_status) }}
            </span>
        </td>

        <td class="px-6 py-5 text-center">
            @php
                $bankStatus = $user->bank_status ?: 'not_submitted';
                $bankClass = [
                    'verified' => 'bg-green-50 text-green-600 border-green-200',
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'rejected' => 'bg-red-50 text-red-600 border-red-200',
                    'not_submitted' => 'bg-navy text-mutedText border-primary/5'
                ][$bankStatus] ?? 'bg-navy text-mutedText border-primary/5';
            @endphp
            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border shadow-sm {{ $bankClass }}">
                {{ str_replace('not_submitted', 'NONE', $bankStatus) }}
            </span>
        </td>

        <td class="px-6 py-5 text-right">
            <span class="font-black text-emerald-600">₹{{ number_format($user->total_earnings ?: 0, 2) }}</span>
        </td>

        <td class="px-6 py-5">
            <div class="flex items-center justify-center text-xs font-bold {{ $user->is_banned ? 'text-secondary' : 'text-green-600' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $user->is_banned ? 'bg-secondary' : 'bg-green-500' }}"></span>
                <span>{{ $user->is_banned ? 'Banned' : 'Active' }}</span>
            </div>
            @if($user->hide_from_leaderboard)
                <div class="mt-1 text-[9px] font-black text-secondary uppercase flex items-center justify-center">
                    <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Hidden
                </div>
            @endif
        </td>

        <td class="px-8 py-5 text-right">
            <div class="flex items-center justify-end gap-1 opacity-90 group-hover:opacity-100 transition-opacity">
                <button @click="viewUser({{ $user->id }})"
                        class="p-2 text-mutedText hover:text-primary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
                        title="View Profile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>

                @if(!$user->trashed())
                    <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-mutedText hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition group-hover:shadow-sm" title="Login as User">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                    <button @click="openModal('edit', @js($user))"
                            class="p-2 text-mutedText hover:text-primary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
                            title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button @click="deleteUser({{ $user->id }})"
                            class="p-2 text-mutedText hover:text-secondary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
                            title="Move to Trash">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                @else
                    <div class="flex items-center gap-2">
                        <button @click="restoreUser({{ $user->id }})"
                                class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-50 hover:bg-green-100 rounded-xl transition border border-green-200">Restore</button>
                        <button @click="forceDelete({{ $user->id }})"
                                class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white bg-secondary rounded-xl shadow-md shadow-secondary/20">Permanent Delete</button>
                    </div>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-12 text-center text-mutedText/50 font-bold">No users found</td>
    </tr>
@endforelse
