@extends('layouts.app')

@section('title', 'Jobs')
@section('page_title', 'Add Job')
@section('breadcrumb_title', 'Jobs / Add Jobs')

@section('wrapper')
    <div class="container-fluid">
        <div class="card smart-card mb-4">
            <div class="card-header smart-card-header">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="bx bx-briefcase me-1"></i>Add Jobs
                </h6>
            </div>

            <div class="card-body">
                <form action="{{ route('jobs.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Job Title -->
                        <div class="col-md-3">
                            <label class="form-label">Job Position Name</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- Company Name -->
                        <div class="col-md-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>

                        <!-- Category -->
                        <div class="col-md-3">
                            <label class="form-label">Department / Category</label>
                            <input type="text" name="category" class="form-control">
                        </div>

                        <!-- Job Type -->
                        <div class="col-md-3">
                            <label class="form-label">Job Type</label>
                            <select name="job_type" class="form-select" required>
                                <option value="">Select</option>
                                <option value="Full-Time">Full-Time</option>
                                <option value="Part-Time">Part-Time</option>
                                <option value="Intern">Intern</option>
                                <option value="Contract">Contract</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div class="col-md-3">
                            <label class="form-label">Job Location</label>
                            <input type="text" name="location" class="form-control">
                        </div>

                        <!-- Salary Min -->
                        <div class="col-md-3">
                            <label class="form-label">Salary Min</label>
                            <input type="number" step="0.01" name="salary_min" class="form-control">
                        </div>

                        <!-- Salary Max -->
                        <div class="col-md-3">
                            <label class="form-label">Salary Max</label>
                            <input type="number" step="0.01" name="salary_max" class="form-control">
                        </div>

                        <!-- Experience Level -->
                        <div class="col-md-3">
                            <label class="form-label">Experience Level</label>
                            <select name="experience_level" class="form-select">
                                <option value="">Select</option>
                                <option value="Entry">Entry</option>
                                <option value="Mid">Mid</option>
                                <option value="Senior">Senior</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Post Date</label>
                            <input type="datetime-local" name="post_date" class="form-control" value="{{ old('post_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Closing Date</label>
                            <input type="datetime-local" name="closing_date" class="form-control"
                                value="{{ old('closing_date') }}">
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Skills -->
                        <div class="col-md-6">
                            <label class="form-label">Skills (comma separated)</label>
                            <textarea name="skills" class="form-control" rows="2" placeholder="PHP, Laravel, MySQL"></textarea>
                        </div>

                        <!-- Description -->
                        <div class="col-md-6">
                            <label class="form-label">Job Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Save Job
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
