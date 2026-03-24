@extends('layouts.app')

@section('style')

@include('training.css')

@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Data Training</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Training Data Generator</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card training-card mt-5">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class='bx bx-brain text-primary' style="font-size: 80px;"></i>
                            </div>
                            <h2 class="mb-3">Generate Training Dataset</h2>
                            <p class="text-muted mb-4">
                                This process will analyze all uploaded resumes and find the best 15 matching job titles for each
                                candidate using the Gemini AI. The resulting data will be saved to the <code>traning_data_sets</code>
                                table for future model training.
                            </p>

                            @if (session('success'))
                                <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
                                    <div class="text-white">{{ session('success') }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                                    <div class="text-white">{{ session('error') }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('info'))
                                <div class="alert alert-info border-0 bg-info alert-dismissible fade show">
                                    <div class="text-white">{{ session('info') }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('training.process') }}" method="POST" id="trainingForm">
                                @csrf
                                <button type="submit" class="btn btn-training btn-lg w-100 mt-3" id="processBtn">
                                    <span class="btn-text">Start Data Training Generation</span>
                                    <div class="loader-container">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Processing Resumes...
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-xl-12 mx-auto">
                    <div class="card shadow-sm border-0" style="border-radius: 10px; background-color: white;">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary d-flex align-items-center gap-2" style="font-weight: 600;">
                                <i class='bx bx-briefcase-alt-2' style="font-size: 24px;"></i> View All Training Data
                            </h5>
                            <div class="d-flex gap-2">
                                {{-- <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importExcelModal" style="border-radius: 8px; font-weight: 500; display: none;">
                                    <i class='bx bx-import'></i> Import Excel
                                </button> --}}
                                <a href="{{ route('training.export') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2" style="border-radius: 8px; font-weight: 500;">
                                    <i class='bx bx-export'></i> Export Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3 mb-4">
                                <div class="col-md-10">
                                    <label class="form-label">Search Training Data</label>
                                    <input type="text" name="search" class="form-control" value="{{ $search ?? '' }}"
                                        placeholder="Certificates, Experiences, Skills, or Job Titles...">
                                </div>
                                <div class="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="{{ route('training.index') }}" class="btn btn-danger w-100">Reset</a>
                                </div>
                            </form>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <form method="GET" id="perPageForm">
                                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>Show</span>
                                        <select name="per_page" class="form-select form-select-sm" style="width: 70px;"
                                            onchange="document.getElementById('perPageForm').submit()">
                                            @foreach ([10, 25, 50, 100] as $size)
                                                <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                                    {{ $size }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span>entries</span>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="border-top: 1px solid #f0f0f0;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr style="border-bottom: 2px solid #e9ecef;">
                                            <th class="ps-3 py-3" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">ID</th>
                                            <th class="py-3" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">Certificates</th>
                                            <th class="py-3" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">Experiences</th>
                                            <th class="py-3" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">Skills</th>
                                            <th class="py-3" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">Matching Job Titles</th>
                                            <th class="pe-3 py-3 text-end" style="font-weight: 700; color: #1a1a1a; letter-spacing: 0.5px;">Generated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trainingData as $row)
                                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                                <td class="ps-3 py-3 text-muted">{{ $row->id }}</td>
                                                <td class="py-3">
                                                    @php $certs = json_decode($row->certificates, true); @endphp
                                                    @if(is_array($certs))
                                                        <div class="text-truncate" style="max-width: 200px; cursor: help;"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ implode(', ', $certs) }}">
                                                            {{ implode(', ', $certs) }}
                                                        </div>
                                                    @else
                                                        {{ $row->certificates }}
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @php $exps = json_decode($row->experiences, true); @endphp
                                                    @if(is_array($exps))
                                                        <div class="text-truncate" style="max-width: 250px; cursor: help;"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ implode(' | ', $exps) }}">
                                                            {{ implode(', ', $exps) }}
                                                        </div>
                                                    @else
                                                        {{ $row->experiences }}
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @php $skills = json_decode($row->skills, true); @endphp
                                                    @if(is_array($skills))
                                                        <div class="text-wrap" style="max-width: 200px; cursor: help;"
                                                             data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="{{ implode(', ', $skills) }}">
                                                            @foreach(array_slice($skills, 0, 5) as $skill)
                                                                <small class="text-muted d-inline-block">{{ $skill }}{{ !$loop->last ? ',' : '' }} </small>
                                                            @endforeach
                                                            @if(count($skills) > 5) ... @endif
                                                        </div>
                                                    @else
                                                        {{ $row->skills }}
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @php $jobs = json_decode($row->matching_job_list, true); @endphp
                                                    @if(is_array($jobs))
                                                        <div style="font-size: 0.85rem; color: #2c3e50; font-weight: 500; cursor: help;"
                                                             data-bs-toggle="popover"
                                                             data-bs-trigger="hover focus"
                                                             data-bs-content="<ul class='mb-0 ps-3 small'>@foreach($jobs as $job)<li>{{ $job }}</li>@endforeach</ul>"
                                                             data-bs-html="true"
                                                             title="Job Recommendations">
                                                            {{ $jobs[0] ?? 'N/A' }}
                                                            @if(count($jobs) > 1)
                                                                <span class="text-muted small"> (+{{ count($jobs) - 1 }} more)</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        {{ $row->matching_job_list }}
                                                    @endif
                                                </td>
                                                <td class="pe-3 py-3 text-end text-muted small">
                                                    {{ $row->created_at->format('Y-m-d H:i:s') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">No training data generated yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Showing {{ $trainingData->firstItem() }} to {{ $trainingData->lastItem() }} of
                                    {{ $trainingData->total() }} entries
                                </div>
                                <div>
                                    {{ $trainingData->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="importExcelModalLabel">Import Training Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('training.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-4 text-center">
                            <i class='bx bx-file-blank text-primary' style="font-size: 64px;"></i>
                            <p class="text-muted mt-2">Select an Excel or CSV file to import training records.</p>
                        </div>

                        <div class="alert alert-info border-0 bg-light-info mb-4" style="font-size: 0.85rem;">
                            <i class='bx bx-info-circle me-1'></i>
                            <strong>Required Columns:</strong> skills, experiences, certificates, matching_job_list
                        </div>

                        <div class="mb-3">
                            <label for="excelFile" class="form-label fw-bold">Choose File</label>
                            <input class="form-control" type="file" id="excelFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text mt-2 text-muted">Max file size: 2MB</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            });

            const trainingForm = document.getElementById('trainingForm');
            if (trainingForm) {
                trainingForm.addEventListener('submit', function() {
                    const btn = document.getElementById('processBtn');
                    btn.classList.add('processing');
                    btn.disabled = true;
                });
            }
        });
    </script>
@endsection
