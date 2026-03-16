@extends('layouts.app')

@section('page_title', 'Client Profile')
@section('breadcrumb_title', 'Client / Client Profile')

@section('wrapper')
    <div class="container-fluid">
        <!-- ================= PROFILE HEADER ================= -->
        <div class="card smart-card mb-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-primary p-4 text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 me-4" style="width: 80px; height: 80px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="mb-1 text-white">{{ $user->name }}</h3>
                            <p class="mb-0 text-white-50">
                                <i class="bx bx-envelope me-1"></i> {{ $user->email }} | 
                                <i class="bx bx-phone me-1"></i> {{ $user->phone ?? 'No Phone' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-primary px-3 py-2 fs-6">
                            Ref: {{ $user->user_ref_no }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Navigation Sidebar -->
            <div class="col-lg-3">
                <div class="card smart-card mb-4">
                    <div class="card-header smart-card-header">
                        <h6 class="mb-0 text-primary fw-semibold"><i class="bx bx-file me-1"></i> Resumes</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="resume-tabs" role="tablist">
                            @forelse($user->cv_lists as $index => $resume)
                                <a class="list-group-item list-group-item-action {{ $index == 0 ? 'active' : '' }}" 
                                   id="resume-tab-{{ $resume->id }}" 
                                   data-bs-toggle="list" 
                                   href="#resume-{{ $resume->id }}" 
                                   role="tab">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 text-inherit">Resume #{{ $index + 1 }}</h6>
                                        <small>{{ $resume->created_at->format('M d, Y') }}</small>
                                    </div>
                                    <small class="text-muted d-block text-truncate">{{ basename($resume->file_path) }}</small>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    No resumes uploaded
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resume Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    @forelse($user->cv_lists as $index => $resume)
                        <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="resume-{{ $resume->id }}" role="tabpanel">
                            @if($resume->parsedData)
                                <!-- Summary -->
                                <div class="card smart-card mb-4 border-start border-4 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary"><i class="bx bx-info-circle me-1"></i> Summary</h5>
                                        <p class="card-text">{{ $resume->parsedData->summary_text }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Strengths -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card smart-card h-100">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="bx bx-trending-up me-1"></i> Strengths</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @forelse($resume->parsedData->strengths as $strength)
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class="bx bx-check-circle text-success me-2"></i>
                                                            {{ $strength->description }}
                                                        </li>
                                                    @empty
                                                        <li class="list-group-item text-muted small">No strengths found</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Weaknesses -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card smart-card h-100">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="mb-0"><i class="bx bx-trending-down me-1"></i> Areas for Improvement</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @forelse($resume->parsedData->weaknesses as $weakness)
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class="bx bx-info-circle text-danger me-2"></i>
                                                            {{ $weakness->description }}
                                                        </li>
                                                    @empty
                                                        <li class="list-group-item text-muted small">No weaknesses found</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Skills -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card smart-card h-100">
                                            <div class="card-header smart-card-header">
                                                <h6 class="mb-0 text-primary fw-semibold"><i class="bx bx-code-alt me-1"></i> Skills & Expertise</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @forelse($resume->parsedData->technicalSkills as $skill)
                                                        <span class="badge bg-light text-primary border border-primary px-3 py-2">
                                                            {{ $skill->description }}
                                                        </span>
                                                    @empty
                                                        <span class="text-muted small">No skills extracted</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Certificates -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card smart-card h-100">
                                            <div class="card-header smart-card-header">
                                                <h6 class="mb-0 text-primary fw-semibold"><i class="bx bx-certification me-1"></i> Certificates</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @forelse($resume->parsedData->certificates as $cert)
                                                        <li class="list-group-item">
                                                            <i class="bx bx-medal text-warning me-2"></i>
                                                            {{ $cert->description }}
                                                        </li>
                                                    @empty
                                                        <li class="list-group-item text-muted small">No certificates found</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Recommendations -->
                                <div class="card smart-card mb-4">
                                    <div class="card-header smart-card-header bg-light">
                                        <h6 class="mb-0 text-primary fw-semibold"><i class="bx bx-briefcase-alt-2 me-1"></i> Compatible Job Roles</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @forelse($resume->parsedData->jobRecommendations as $job)
                                                <div class="col-md-6 mb-3">
                                                    <div class="p-3 bg-light rounded border-start border-3 border-primary h-100">
                                                        <h6 class="mb-1 text-primary">{{ $job->job_title }}</h6>
                                                        <p class="small text-muted mb-0">{{ Str::limit($job->job_description, 100) }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center text-muted p-4">
                                                    No job recommendations available
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Resume File -->
                                <div class="card smart-card">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Original Resume File</h6>
                                            <p class="small text-muted mb-0">{{ basename($resume->file_path) }}</p>
                                        </div>
                                        <a href="{{ asset('storage/' . $resume->file_path) }}" target="_blank" class="btn btn-outline-primary">
                                            <i class="bx bx-download me-1"></i> Download / View
                                        </a>
                                    </div>
                                </div>

                            @else
                                <div class="card smart-card p-5 text-center">
                                    <div class="mb-3 fs-1 text-muted">
                                        <i class="bx bx-spreadsheet"></i>
                                    </div>
                                    <h5>No Parsed Data Available</h5>
                                    <p class="text-muted">This resume hasn't been analyzed by the system yet.</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="card smart-card p-5 text-center">
                            <div class="mb-3 fs-1 text-muted">
                                <i class="bx bx-user-x"></i>
                            </div>
                            <h5>No Profile Data</h5>
                            <p class="text-muted">The client hasn't uploaded any resumes to analyze.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Future enhancements if needed
        });
    </script>
@endsection
