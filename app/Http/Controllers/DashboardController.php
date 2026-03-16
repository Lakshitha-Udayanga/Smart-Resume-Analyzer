<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $totalClients = User::where('is_system_user', 0)->count();
        $activeClients = User::where('is_system_user', 0)->has('cv_lists')->count();
        $activeJobs = Job::where('status', 'active')->count();
        $inactiveJobs = Job::where('status', 'inactive')->count();

        return view('home.index', compact('totalClients', 'activeClients', 'activeJobs', 'inactiveJobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
        //
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
