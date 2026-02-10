@extends('layouts.app')

@section('title', 'Jobs')
@section('page_title', 'Jobs List')
@section('breadcrumb_title', 'Jobs / Jobs List')

@section('wrapper')
    <div class="container-fluid">

        <!-- ================= FILTER CARD ================= -->
        <div class="card smart-card mb-4">
            <div class="card-header smart-card-header">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="bx bx-briefcase me-1"></i> Jobs
                </h6>
            </div>

            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Post Start Date</label>
                        <input type="date" name="post_date" class="form-control" value="{{ $post_date ?? '' }}"
                            placeholder="YYYY-MM-DD - YYYY-MM-DD">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Post End Date</label>
                        <input type="date" name="closing_date" class="form-control" value="{{ $closing_date ?? '' }}"
                            placeholder="YYYY-MM-DD - YYYY-MM-DD">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ ($status ?? '') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button> &nbsp;&nbsp;
                        <button type="submit" class="btn btn-danger">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= TABLE CARD ================= -->
        <div class="card smart-card">
            <div class="card-header smart-card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary fw-semibold"> <i class="bx bx-briefcase me-1"></i> View All Jobs</h6>
                <div class="btn-group">
                    <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Add
                    </a>
                </div>
            </div>

            <div class="card-body">

                <!-- SHOW ENTRIES -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <form method="GET" id="perPageForm">
                        @foreach (request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label>
                            Show
                            <select name="per_page" class="form-select d-inline-block w-auto"
                                onchange="document.getElementById('perPageForm').submit()">
                                @foreach ([5, 10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                        {{ $size }}</option>
                                @endforeach
                            </select>
                            entries
                        </label>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="jobs_datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Post Date</th>
                                <th>Job Position Name</th>
                                <th>Company Name</th>
                                <th>Department</th>
                                <th>Job Type</th>
                                <th>Job Location</th>
                                <th>Closing Date</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td>{{ $job->post_date }}</td>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->company_name }}</td>
                                    <td>{{ $job->category }}</td>
                                    <td>{{ $job->job_type }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>{{ $job->closing_date }}</td>
                                    <td>{{ Str::limit($job->description, 50) }}</td>
                                    <td>
                                        @if ($job->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('jobs.edit', $job->id) }}"
                                            class="btn btn-sm btn-primary px-3 py-1">Edit</a>
                                        <form action="{{ route('jobs.destroy', $job->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this job?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No jobs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of
                            {{ $jobs->total() }}
                            entries
                        </div>
                        <div id="paginationLinks">
                            {{ $jobs->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection
