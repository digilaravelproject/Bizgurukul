@extends('layouts.admin')
@section('title', 'Community Management')

@section('content')
    <div x-data="communityManager()" class="space-y-8 animate-fade-in-down">
        {{-- Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-800 uppercase">Community Links</h2>
                <p class="text-sm text-slate-500 mt-1 font-medium">
                    Manage your social connections and community groups.
                </p>
            </div>

            <button @click="openModal('add')"
                class="group relative inline-flex items-center justify-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-xs font-black text-white uppercase tracking-widest shadow-xl transition-all duration-300 hover:bg-slate-800 hover:-translate-y-1 active:scale-95">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Add More Link
            </button>
        </div>

        {{-- Community Groups --}}
        @foreach ($communities as $groupName => $groupItems)
            <div class="space-y-4">
                <h3 class="text-lg font-black text-slate-700 uppercase tracking-wider flex items-center gap-3">
                    <span class="w-8 h-1 bg-slate-900 rounded-full"></span>
                    {{ $groupName }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($groupItems as $item)
                        <div class="bg-white border border-slate-200 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="space-y-1">
                                    <h4 class="text-xl font-bold text-slate-800">{{ $item->name }}</h4>
                                    <p class="text-xs text-slate-500">{{ $item->description }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.communities.toggle', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $item->is_active ? 'bg-green-500' : 'bg-slate-300' }}">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $item->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Target Link</div>
                                <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-2 text-sm text-slate-600 truncate font-mono">
                                    {{ $item->link ?? 'No Link Set' }}
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-50 flex justify-between items-center">
                                <button @click="openModal('edit', {{ $item->toJson() }})"
                                    class="text-xs font-black text-slate-900 uppercase tracking-widest hover:text-slate-600 transition-colors">
                                    Edit Details
                                </button>

                                @if($item->is_custom)
                                    <form action="{{ route('admin.communities.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bhai, are you sure? It will delete this link forever.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Modal --}}
        <div x-show="showModal"
             class="fixed inset-0 z-[100] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div @click="showModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <div class="relative bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full animate-fade-in-up p-8">
                    <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tight mb-6" x-text="modalMode === 'add' ? 'Add New Community' : 'Edit Community Link'"></h3>

                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="modalMode === 'edit'">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Display Name</label>
                                <input type="text" name="name" x-model="formData.name" required
                                    class="w-full h-12 px-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/20 font-bold text-slate-800" placeholder="e.g. Telegram Channel">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Short Description</label>
                                <input type="text" name="description" x-model="formData.description"
                                    class="w-full h-12 px-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/20 font-bold text-slate-800" placeholder="e.g. Join our official group">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">URL / Link</label>
                                <input type="url" name="link" x-model="formData.link" required
                                    class="w-full h-12 px-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/20 font-bold text-slate-800 font-mono text-sm" placeholder="https://t.me/yourid">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Button Text</label>
                                <input type="text" name="button_text" x-model="formData.button_text" required
                                    class="w-full h-12 px-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/20 font-bold text-slate-800" placeholder="Join Now">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Group Section</label>
                                <select name="group_name" x-model="formData.group_name" required
                                    class="w-full h-12 px-5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/20 font-bold text-slate-800">
                                    <option value="Join Our Communities">Join Our Communities</option>
                                    <option value="Join Our Social Media Channels">Join Our Social Media Channels</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" @click="showModal = false"
                                class="flex-1 h-14 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 transition-all text-center">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-[2] h-14 brand-slate bg-slate-900 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-slate-200 hover:-translate-y-1 transition-all">
                                <span x-text="modalMode === 'add' ? 'Save Community' : 'Update Connection'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function communityManager() {
            return {
                showModal: false,
                modalMode: 'add',
                formAction: "{{ route('admin.communities.store') }}",
                formData: {
                    name: '',
                    description: '',
                    link: '',
                    button_text: 'Join Now',
                    group_name: 'Join Our Communities'
                },

                openModal(mode, data = null) {
                    this.modalMode = mode;
                    if (mode === 'edit' && data) {
                        this.formData = {
                            name: data.name,
                            description: data.description,
                            link: data.link,
                            button_text: data.button_text,
                            group_name: data.group_name
                        };
                        this.formAction = `/admin/communities/${data.id}`;
                    } else {
                        this.formData = {
                            name: '',
                            description: '',
                            link: '',
                            button_text: 'Join Now',
                            group_name: 'Join Our Communities'
                        };
                        this.formAction = "{{ route('admin.communities.store') }}";
                    }
                    this.showModal = true;
                }
            }
        }
    </script>
@endsection
