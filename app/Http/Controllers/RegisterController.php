<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name'  => 'required|string|max:100',
                'phone'      => 'required|string|max:15|unique:users,phone',
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|min:6',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'name'       => $validated['first_name'] . ' ' . $validated['last_name'],
                'phone'      => $validated['phone'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'api_token'  => Str::random(80),
                'user_ref_no' => str_pad(User::max('id'), 5, '0', STR_PAD_LEFT),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'user'   => $user,
                'token'  => $user->api_token
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'msg'    => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

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
