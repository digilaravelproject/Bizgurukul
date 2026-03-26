@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="payoutManager()" x-init="init()">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-mainText uppercase tracking-widest"><i class="fas fa-hand-holding-usd text-primary"></i> Payout Management</h2>
            <p class="text-xs text-mutedText font-semibold mt-1">Review and process partner withdrawal requests securely.</p>
        </div>

        {{-- Entries Per Page Selector --}}
        <div class="flex items-center gap-3 bg-surface px-4 py-2 rounded-2xl border border-primary/10 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Show</span>
            <select x-model="perPage" @change="fetchPayouts()" class="bg-transparent text-sm font-black text-primary outline-none cursor-pointer">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Entries</span>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div x-show="isLoading" x-transition.opacity class="absolute inset-0 bg-surface/60 backdrop-blur-[2px] z-10 flex items-center justify-center" style="display: none;">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-circle-notch fa-spin text-3xl text-primary"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-primary animate-pulse">Updating...</span>
            </div>
        </div>

        <div id="payouts-container">
            @include('admin.payouts.partials.payout_table')
        </div>
    </div>
</div>

@push('scripts')
<script>
    function payoutManager() {
        return {
            perPage: 20,
            isLoading: false,

            init() {
                // Listen for pagination clicks
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('.ajax-pagination a');
                    if (link) {
                        e.preventDefault();
                        this.fetchPayouts(link.href);
                    }
                });
            },

            async fetchPayouts(url = null) {
                this.isLoading = true;
                
                try {
                    // Build URL with per_page parameter
                    let fetchUrl = url ? new URL(url) : new URL(window.location.href);
                    fetchUrl.searchParams.set('per_page', this.perPage);
                    
                    const response = await fetch(fetchUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const html = await response.text();
                    document.getElementById('payouts-container').innerHTML = html;
                    
                    // Update URL in browser for bookmarking/refresh without reload
                    window.history.pushState({}, '', fetchUrl);

                } catch (error) {
                    console.error('Error fetching payouts:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong while loading the data!',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    });
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
