<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeListController extends Controller
{
    public function index()
    {
        $perPage = request()->input('per_page', 10);
        $search = request()->input('search');

        $query = Resume::with(['parsedData', 'user'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })->orWhere('file_path', 'LIKE', "%{$search}%");
            });
        }

        $resumes = $query->paginate($perPage)->appends(request()->all());

        return view('resumes.index', compact('resumes', 'perPage', 'search'));
    }

    public function destroy($id)
    {
        $resume = Resume::findOrFail($id);
        $resume->delete();
        return back()->with('success', 'Resume deleted successfully.');
    }
}
