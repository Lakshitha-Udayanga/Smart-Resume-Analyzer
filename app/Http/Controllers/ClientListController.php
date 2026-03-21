<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\ClientResumeExport;
use Maatwebsite\Excel\Facades\Excel;

class ClientListController extends Controller
{

    public function exportExcel()
    {
        return Excel::download(new ClientResumeExport, 'clients_resumes.xlsx');
    }

    public function index()
    {
        $users = User::query()->where('is_system_user', 0);
        $perPage = request()->input('per_page', 10);
        $search = request()->input('search');
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');

        if ($search) {
            $users->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('user_ref_no', 'LIKE', "%{$search}%");
            });
        }

        if ($start_date && $end_date) {
            $users->whereBetween('created_at', [
                \Carbon\Carbon::parse($start_date)->startOfDay(),
                \Carbon\Carbon::parse($end_date)->endOfDay()
            ]);
        }

        $users = $users->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->all());

        return view('registered_client.index', compact('users', 'perPage', 'search', 'start_date', 'end_date'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with([
            'cv_lists.parsedData.strengths',
            'cv_lists.parsedData.weaknesses',
            'cv_lists.parsedData.technical_skills',
            'cv_lists.parsedData.soft_skills',
            'cv_lists.parsedData.certificates',
            'cv_lists.parsedData.job_recommendations'
        ])->findOrFail($id);

        return view('registered_client.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
