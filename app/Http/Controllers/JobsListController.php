<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class JobsListController extends Controller
{

    public function index()
    {
        $status = request()->input('status');
        $post_date = request()->input('post_date');
        $closing_date = request()->input('closing_date');
        $perPage = request()->input('per_page', 10);

        $query = Job::query();

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        if ($post_date && $closing_date) {
            $query->whereBetween('post_date', [
                Carbon::parse($post_date)->startOfDay(),
                Carbon::parse($closing_date)->endOfDay()
            ]);
        }

        $jobs = $query->orderBy('post_date', 'desc')
            ->paginate($perPage)
            ->appends(request()->all());

        return view('jobs.index', compact('jobs', 'perPage', 'status', 'post_date', 'closing_date'));
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'job_type' => 'required|in:Full-Time,Part-Time,Intern,Contract',
                'salary_min' => 'nullable|numeric',
                'salary_max' => 'nullable|numeric',
            ]);

            DB::beginTransaction();

            Job::create([
                'title' => $request->title,
                'company_name' => $request->company_name,
                'category' => $request->category,
                'job_type' => $request->job_type,
                'location' => $request->location,
                'salary_min' => $request->salary_min,
                'salary_max' => $request->salary_max,
                'experience_level' => $request->experience_level,
                'skills' => $request->skills,
                'description' => $request->description,
                'status' => $request->status ?? 'active',
                'post_date' => Carbon::parse($request->post_date),
                'closing_date' => Carbon::parse($request->closing_date),
            ]);

            DB::commit();

            return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Job Store Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the job');
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $job = Job::findOrFail($id);

        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, string $id)
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'job_type' => 'required|string|max:50',
                'location' => 'required|string|max:255',
                'post_date' => 'required|date',
                'closing_date' => 'required|date|after_or_equal:post_date',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            $job = Job::findOrFail($id);

            DB::beginTransaction();

            $job->update([
                'title' => $validated['title'],
                'company_name' => $validated['company_name'],
                'category' => $validated['category'],
                'job_type' => $validated['job_type'],
                'location' => $validated['location'],
                'post_date' => Carbon::parse($validated['post_date']),
                'closing_date' => Carbon::parse($validated['closing_date']),
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('jobs.index')->with('success', 'Job updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Job Update Error', ['message' => $e->getMessage(),]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while update the job');
        }
    }

    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();

            $job = Job::findOrFail($id);
            $job->delete();

            DB::commit();

            return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Job Delete Error', [$e]);
        }
    }
}
