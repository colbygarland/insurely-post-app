<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    public function list(){
        return view('users', [
            'users' => User::all()
        ]);
    }

    public function verify(Request $request, string $id){
        $user = User::find($id);
        $user->verified_at = Carbon::now();
        $user->save();

        Session::flash('successMessage', 'User successfully verified.');
        return redirect('/users');
    }
}
