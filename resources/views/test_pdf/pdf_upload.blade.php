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

    <!-- Display success or error message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ url('/pdf/summarize') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="pdf" class="form-label">Select PDF</label>
            <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload & Summarize</button>
    </form>

    @if(isset($summary))
        <div class="mt-4">
            <h4>Summary:</h4>
            <div class="border p-3">{{ $summary }}</div>
        </div>
    @endif
</div>
</body>
</html>
