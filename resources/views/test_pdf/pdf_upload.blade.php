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
                </div>

                @if (isset($job_recommendations) && count($job_recommendations) > 0)
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header bg-dark text-white">
                                    Job Matching Analysis
                                </div>
                                <div class="card-body">
                                    <div style="max-width: 400px; margin: auto;">
                                        <canvas id="jobMatchChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header bg-dark text-white">
                                    Top Job Recommendations
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        @foreach ($job_recommendations as $rec)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1 text-primary">{{ $rec['title'] }}</h6>
                                                    <span
                                                        class="badge bg-success rounded-pill">{{ $rec['final_score'] }}%
                                                        Match</span>
                                                </div>
                                                <p class="mb-1 text-muted">{{ $rec['company_name'] }}</p>
                                                <small class="text-info">
                                                    <strong>Matched Skills:</strong>
                                                    {{ implode(', ', $rec['matched_skills'] ?? []) }}
                                                </small>
                                                <p class="mb-1 text-muted">{{ $rec['link']  ?? ''}}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const ctx = document.getElementById('jobMatchChart').getContext('2d');
                            const jobTitles = @json(collect($job_recommendations)->pluck('title'));
                            const jobScores = @json(collect($job_recommendations)->pluck('final_score'));

                            new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: jobTitles,
                                    datasets: [{
                                        data: jobScores,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.7)',
                                            'rgba(54, 162, 235, 0.7)',
                                            'rgba(255, 206, 86, 0.7)',
                                            'rgba(75, 192, 192, 0.7)',
                                            'rgba(153, 102, 255, 0.7)',
                                            'rgba(255, 159, 64, 0.7)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.label + ': ' + context.raw + '% Match';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                @endif

            </div>
        @endif
    </div>
</body>

</html>
