<!DOCTYPE html>
<html>

<head>
    <title>Upload PDF for Summary</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Upload PDF to Summarize</h2>
            <div class="d-flex gap-2">
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class='bx bx-arrow-back'></i> Back to Dashboard
                </a>
                <a href="{{ route('resumes.export') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2">
                    <i class="bx bx-file me-1"></i> Export to CV Details
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/pdf/summarize') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="pdf" class="form-label">Select PDF</label>
                <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf"
                    required>
                <div id="file-name-display" class="mt-2 text-muted fw-bold" style="display: none;">
                    <i class='bx bx-file'></i> Selected File: <span id="file-name-text"></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Upload & Summarize</button>
                <a href="{{ url('/view-pdf') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        @if (isset($ai_result))
            <div class="mt-5">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Professional Summary</h5>
                        <a href="{{ route('resumes.export') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-file me-1"></i> Export CV Data
                        </a>
                    </div>
                    <div class="card-body">
                        {{ $ai_result['summary'] ?? 'No summary available.' }}
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-success text-white">
                                Strengths
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['strengths']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($ai_result['strengths'] as $strength)
                                            <li class="list-group-item">
                                                ✅ {{ $strength }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No strengths detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-danger text-white">
                                Weaknesses
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['weaknesses']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($ai_result['weaknesses'] as $weakness)
                                            <li class="list-group-item">
                                                ⚠️ {{ $weakness }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No weaknesses detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-info text-white">
                                Skills
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['technical_skills']))
                                    @foreach ($ai_result['technical_skills'] as $skill)
                                        <span class="badge bg-secondary me-2 mb-2">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                @else
                                    <p class="text-muted">No skills detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-warning text-dark">
                                Certificates
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['certifications']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($ai_result['certifications'] as $cert)
                                            <li class="list-group-item">
                                                📜 {{ $cert }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No certificates detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-warning text-dark">
                                Experiences
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['experiences']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($ai_result['experiences'] as $cert)
                                            <li class="list-group-item">
                                                📜 {{ $cert }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No experiences detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-warning text-dark">
                                Soft skills
                            </div>
                            <div class="card-body">
                                @if (!empty($ai_result['soft_skills']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($ai_result['soft_skills'] as $cert)
                                            <li class="list-group-item">
                                                📜 {{ $cert }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No soft_skills detected.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @if (isset($job_recommendations['best_match']))
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header bg-dark text-white">
                                    Recommended Job Title
                                </div>
                                <div class="card-body">
                                    @php
                                        $rec = $job_recommendations['best_match'] ?? null;
                                    @endphp

                                    @if ($rec)
                                        <div
                                            class="d-flex justify-content-between align-items-center p-3 border rounded bg-light">
                                            <div>
                                                <h5 class="mb-0 text-primary fw-bold text-uppercase">
                                                    {{ $rec['job_title'] ?? 'N/A' }}
                                                </h5>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success rounded-pill px-4 py-2 fs-6">
                                                    {{ number_format($rec['match_percentage'] ?? 0, 2) }}% Match
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No recommendation available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Matching Job Openings</h5>
                                    @if(isset($jobs_list))
                                        <span class="badge bg-primary">{{ count($jobs_list) }} Found</span>
                                    @endif
                                </div>
                                <div class="card-body p-0">
                                    @if (isset($jobs_list) && count($jobs_list) > 0)
                                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                            @foreach ($jobs_list as $job)
                                                <div class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1 fw-bold d-inline">{{ $job->title }}</h6>
                                                            <span class="text-muted small ms-2">- {{ $job->experience_level }}</span>
                                                        </div>
                                                        <small class="text-muted">{{ $job->location }}</small>
                                                    </div>
                                                    <p class="mb-1 text-muted small">{{ $job->company_name }}</p>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <div>
                                                            <span class="badge bg-info text-dark small">{{ $job->job_type }}</span>
                                                            @if($job->salary_min)
                                                                <span class="text-success small ms-2">
                                                                    LKR {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if ($job->link)
                                                            <a href="{{ $job->link }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                View Details
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-4 text-center">
                                            <p class="text-muted mb-0">No active job openings found matching this title in our database.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.getElementById('pdf').addEventListener('change', function() {
            const fileNameDisplay = document.getElementById('file-name-display');
            const fileNameText = document.getElementById('file-name-text');
            
            if (this.files && this.files.length > 0) {
                fileNameText.textContent = this.files[0].name;
                fileNameDisplay.style.display = 'block';
            } else {
                fileNameDisplay.style.display = 'none';
            }
        });
    </script>
</body>

</html>
