@extends('layouts.app')

@section('page_title', 'Resume List')
@section('breadcrumb_title', 'Resumes / Resume List')

@section('wrapper')
    <div class="container-fluid">
        <div class="card smart-card">
            <div class="card-header smart-card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="bx bx-file me-1"></i> View All Resumes
                </h6>
            </div>

            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-10">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ $search ?? '' }}"
                            placeholder="Candidate Name, Email, or Filename...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('resumes.index') }}" class="btn btn-danger w-100">Reset</a>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" id="perPageForm">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <label class="d-flex align-items-center gap-2">
                            <span>Show</span>
                            <select name="per_page" class="form-select form-select-sm w-auto"
                                onchange="document.getElementById('perPageForm').submit()">
                                @foreach ([10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </label>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Candidate</th>
                                <th>File Path</th>
                                <th>Parsed Data</th>
                                <th>Uploaded At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($resumes as $resume)
                                <tr>
                                    <td>{{ $resume->id }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $resume->user->name ?? 'Unknown' }}</span>
                                            <small class="text-muted">{{ $resume->user->email ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ basename($resume->file_path) }}</td>
                                    <td>
                                        @if ($resume->parsedData)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $resume->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('client.show', $resume->user_id) }}" class="btn btn-sm btn-outline-primary" title="View Portfolio">
                                                <i class="bx bx-user"></i>
                                            </a>
                                            <form action="{{ route('resumes.destroy', $resume->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No resumes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $resumes->firstItem() }} to {{ $resumes->lastItem() }} of
                        {{ $resumes->total() }} entries
                    </div>
                    <div>
                        {{ $resumes->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
