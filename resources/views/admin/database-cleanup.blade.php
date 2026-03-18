@extends('layouts.admin')

@section('title', 'System Purge & Production Prep')

@section('content')
<div class="container-fluid py-5">
    <!-- Premium Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <div class="p-5 rounded-4 shadow-lg border-0" 
                 style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; opacity: 0.1;">
                    <i class="fas fa-database fa-10x text-white"></i>
                </div>
                <div class="position-relative z-index-1">
                    <h1 class="display-4 fw-bold text-white mb-3">System Purification</h1>
                    <p class="lead text-white-50 mb-0">Prepare your platform for live production by stripping all test artifacts.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if(session('error'))
    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4 p-4 d-flex align-items-center">
        <i class="fas fa-times-circle fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">Execution Aborted</h5>
            <p class="mb-0">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Main Grid -->
    <div class="row g-4">
        <!-- Sidebar: Protections -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-shield-halved text-success me-2"></i> Survival List
                    </h5>
                    <p class="text-muted small mb-0">These assets will never be touched.</p>
                </div>
                <div class="card-body bg-light bg-opacity-50">
                    <h6 class="text-uppercase text-muted fw-bold small mb-3">Elite Admin Access</h6>
                    @foreach($protectedEmails as $email)
                    <div class="d-flex align-items-center mb-3 p-3 bg-white rounded-3 shadow-sm border-start border-4 border-success">
                        <div class="avatar-sm flex-shrink-0 me-3 bg-success bg-opacity-10 rounded-circle text-center" style="width: 40px; height: 40px; line-height: 40px;">
                            <i class="fas fa-user-shield text-success"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 text-truncate">{{ $email }}</h6>
                            <span class="text-success small fw-medium">Primary Root</span>
                        </div>
                    </div>
                    @endforeach

                    <hr class="my-4">

                    <h6 class="text-uppercase text-muted fw-bold small mb-3">Structural Integrity</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['Courses', 'Bundles', 'Lessons', 'Settings', 'Payments', 'Schema'] as $safe)
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 border border-primary border-opacity-25">
                            <i class="fas fa-lock me-1"></i> {{ $safe }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="col-lg-8">
            <!-- Cleanup Results (If executed) -->
            @if($cleaned)
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-5 border-success position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold text-success mb-0">
                            <i class="fas fa-sparkles me-2"></i> Environment Purified
                        </h5>
                        <span class="badge bg-success px-3 py-2">Success Rate: 100%</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Target Asset</th>
                                    <th class="text-center">Records Purged</th>
                                    <th>Resolution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $res)
                                <tr class="border-bottom border-light">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 bg-{{ $res['status'] }} bg-opacity-10 rounded-3 me-3">
                                                <i class="fas fa-server text-{{ $res['status'] }}"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $res['table'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold {{ $res['deleted'] > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ number_format($res['deleted']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-{{ $res['status'] }} small">
                                            <i class="fas fa-check-circle me-1"></i> {{ $res['message'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Purge completed in <strong>{{ $totalTime }}s</strong></span>
                        <a href="{{ route('admin.database-cleanup') }}" class="btn btn-sm btn-outline-primary px-4 rounded-pill">Reset Terminal</a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Impact Analysis -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Environment Audit</h5>
                        <p class="text-muted small mb-0">Current footprint before purification.</p>
                    </div>
                    @if(!$cleaned)
                    <button type="button" class="btn btn-danger btn-lg px-4 rounded-pill shadow-sm animate-pulse" 
                            onclick="triggerPurge()" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); border: 0;">
                        <i class="fas fa-bolt-lightning me-2"></i> Commence Purge
                    </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-4 py-3">Database Table</th>
                                    <th class="text-center">Total Entries</th>
                                    <th class="pe-4 text-end">Security Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tables as $table)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <code class="px-2 py-1 bg-light rounded text-primary">{{ $table['name'] }}</code>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium {{ $table['count'] > 0 ? 'text-dark' : 'text-muted' }}">
                                            {{ number_format($table['count']) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if($table['type'] == 'safe')
                                            <span class="badge bg-success bg-opacity-10 text-success p-2 px-3">
                                                <i class="fas fa-lock me-1"></i> Immutable
                                            </span>
                                        @elseif($table['type'] == 'cleanup')
                                            <span class="badge bg-danger bg-opacity-10 text-danger p-2 px-3">
                                                <i class="fas fa-trash-can me-1"></i> Target
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning p-2 px-3">
                                                <i class="fas fa-filter me-1"></i> Selective
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Action Form -->
<form id="purgeForm" action="{{ route('admin.database-cleanup.execute') }}" method="POST" class="d-none">
    @csrf
</form>

<style>
    .rounded-4 { border-radius: 1.25rem !important; }
    .animate-pulse {
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(220, 38, 38, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
</style>

<script>
    function triggerPurge() {
        Swal.fire({
            title: 'Initiate System Purge?',
            text: "This action will permanently wipe all test data. ONLY structurally critical tables and white-listed admin accounts will survive. This is irreversible!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-skull-crossbones me-2"></i> Yes, Wipe Everything',
            cancelButtonText: 'Abort Mission',
            padding: '2rem',
            background: '#fff',
            borderRadius: '1rem',
            backdrop: `rgba(0,0,123,0.4)`
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Purifying Environment...',
                    html: 'Please do not close this window. Stripping test data and clearing caches.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                document.getElementById('purgeForm').submit();
            }
        })
    }
</script>
@endsection
