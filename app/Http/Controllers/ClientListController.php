<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ClientListController extends Controller
{

    public function index()
    {
        $users = User::query();
        $perPage = request()->input('per_page', 10);

        $users = $users->where('is_system_user', 0)->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->all());

        return view('registered_client.index', compact('users', 'perPage'));
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
