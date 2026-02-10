@extends('layouts.app')

@section('title', 'Edit Job')
@section('page_title', 'Edit Job')
@section('breadcrumb_title', 'Jobs / Edit')

@section('wrapper')
    <div class="container-fluid">

        <div class="card smart-card mb-4">
            <div class="card-header smart-card-header">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="bx bx-briefcase me-1"></i>Edit Jobs
                </h6>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('jobs.update', $job->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Job Position Name</label>
                            <input type="text" name="title" value="{{ old('title', $job->title) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $job->company_name) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Department / Category</label>
                            <input type="text" name="category" value="{{ old('category', $job->category) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Job Type</label>
                            <input type="text" name="job_type" value="{{ old('job_type', $job->job_type) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Job Location</label>
                            <input type="text" name="location" value="{{ old('location', $job->location) }}"
                                class="form-control" required>
                        </div>

                        <!-- Salary Min -->
                        <div class="col-md-3">
                            <label class="form-label">Salary Min</label>
                            <input type="number" step="0.01" name="salary_min" class="form-control"
                                value="{{ old('salary_min', $job->salary_min) }}">
                        </div>

                        <!-- Salary Max -->
                        <div class="col-md-3">
                            <label class="form-label">Salary Max</label>
                            <input type="number" step="0.01" name="salary_max" class="form-control"
                                value="{{ old('salary_max', $job->salary_max) }}">
                        </div>

                        <!-- Experience Level -->
                        <div class="col-md-3">
                            <label class="form-label">Experience Level</label>
                            <select name="experience_level" class="form-select">
                                <option value="">Select</option>
                                <option value="Entry" {{ $job->experience_level == 'Entry' ? 'selected' : '' }}>Entry
                                </option>
                                <option value="Mid" {{ $job->experience_level == 'Mid' ? 'selected' : '' }}>Mid</option>
                                <option value="Senior" {{ $job->experience_level == 'Senior' ? 'selected' : '' }}>Senior
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Post Date</label>
                            <input type="datetime-local" name="post_date"
                                value="{{ old('post_date', \Carbon\Carbon::parse($job->post_date)->format('Y-m-d\TH:i')) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Closing Date</label>
                            <input type="datetime-local" name="closing_date"
                                value="{{ old('post_date', \Carbon\Carbon::parse($job->closing_date)->format('Y-m-d\TH:i')) }}"
                                class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ $job->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $job->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <!-- Skills -->
                        <div class="col-md-6">
                            <label class="form-label">Skills (comma separated)</label>
                            <textarea name="skills" class="form-control" rows="2" placeholder="PHP, Laravel, MySQL">{{ old('description', $job->skills) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $job->description) }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update Job
                            </button>
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
