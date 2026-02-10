@extends('layouts.app')

@section('page_title', 'Client List')
@section('breadcrumb_title', 'Client / Client List')

@section('wrapper')
    <div class="container-fluid">
        <!-- ================= TABLE CARD ================= -->
        <div class="card smart-card">
            <div class="card-header smart-card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary fw-semibold">
                    <i class="bx bx-group me-1"></i> View All Clients
                </h6>
            </div>

            <div class="card-body">
                <!-- SHOW ENTRIES -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" id="perPageForm">
                        @foreach (request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <label class="d-flex align-items-center gap-2">
                            <span>Show</span>
                            <select name="per_page" class="form-select form-select-sm w-auto"
                                onchange="document.getElementById('perPageForm').submit()">
                                @foreach ([5, 10, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <span>entries</span>
                        </label>
                    </form>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone No</th>
                                <th>Ref No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->user_ref_no }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No clients found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of
                        {{ $users->total() }}
                        entries
                    </div>
                    <div id="paginationLinks">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // future JS
    </script>
@endsection
