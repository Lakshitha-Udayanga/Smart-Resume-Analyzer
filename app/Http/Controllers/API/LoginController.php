<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->with('cv_lists')->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            return response()->json([
                'user'  => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg'  => $$th->getMessage(),
            ]);
        }
    }

    public function index()
    {
        $users = User::get();

        return response()->json([
            'users'  => $users,
        ]);
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
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                foreach ($user->cv_lists as $resume) {
                    $parsedData = $resume->parsedData;
                    if ($parsedData) {
                        $parsedData->strengths()->delete();
                        $parsedData->weaknesses()->delete();
                        $parsedData->technical_skills()->delete();
                        $parsedData->soft_skills()->delete();
                        $parsedData->certificates()->delete();
                        $parsedData->experiences()->delete();
                        $parsedData->job_recommendations()->delete();
                        $parsedData->delete();
                    }
                    $resume->delete();
                }

                // Delete related notifications
                \App\Models\Notification::where('notifiable_id', $user->id)
                    ->where('type', 'client')
                    ->delete();

                $user->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'User and all associated data deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
