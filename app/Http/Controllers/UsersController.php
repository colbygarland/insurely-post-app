<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    public function list()
    {
        return view('users', [
            'users' => User::all(),
        ]);
    }

    public function unauthorized()
    {
        // If user is authenticated and verified, redirect to dashboard
        if (Auth::check() && Auth::user()->verified_at) {
            return redirect()->route('dashboard');
        }

        return view('unauthorized');
    }

    public function verify(Request $request, string $id)
    {
        $user = User::find($id);
        $user->verified_at = Carbon::now();
        $user->save();

        Session::flash('successMessage', 'User successfully verified.');

        return redirect('/users');
    }

    public function promoteToAdmin(Request $request, string $id)
    {
        $user = User::find($id);

        if (! $user) {
            Session::flash('errorMessage', 'User not found.');

            return redirect('/users');
        }

        if ($user->role === 'admin') {
            Session::flash('errorMessage', 'User is already an admin.');

            return redirect('/users');
        }

        $user->role = 'admin';
        $user->save();

        Session::flash('successMessage', 'User successfully promoted to admin.');

        return redirect('/users');
    }

    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);
        $user->delete();

        Session::flash('successMessage', 'User successfully deleted.');

        return redirect('/users');
    }
}
