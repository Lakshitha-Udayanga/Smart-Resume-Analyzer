<!DOCTYPE html>
<html>

<head>
    <title>Upload PDF for Summary</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Upload PDF to Summarize</h2>

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
            <button type="submit" class="btn btn-primary">Upload & Summarize</button>
        </form>

        @if (isset($aiResult))
            <div class="mt-5">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Professional Summary</h5>
                    </div>
                    <div class="card-body">
                        {{ $aiResult['summary'] ?? 'No summary available.' }}
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header bg-success text-white">
                                Strengths
                            </div>
                            <div class="card-body">
                                @if (!empty($aiResult['strengths']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($aiResult['strengths'] as $strength)
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
                                @if (!empty($aiResult['weaknesses']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($aiResult['weaknesses'] as $weakness)
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
                                @if (!empty($aiResult['skills']))
                                    @foreach ($aiResult['skills'] as $skill)
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
                                @if (!empty($aiResult['certificates']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($aiResult['certificates'] as $cert)
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
                                @if (!empty($aiResult['experiences']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($aiResult['experiences'] as $cert)
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
                                @if (!empty($aiResult['soft_skills']))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($aiResult['soft_skills'] as $cert)
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
            </div>
        @endif

    </div>
</body>

</html>
