<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index()
    {
        $employees = Employee::where('outlet_id', session('outlet_id'))->get();
        return view('user.index', [
            'users' => $employees
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->hasRole('owner')) {
            return redirect()->back();
        }

        return view('user.create');
    }

    public function store(Request $request)
    {
        Validator::extend('without_spaces', function ($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });
        $validateddata = $request->validate([
            'level_id' => 'required',
            'name' => 'required|min:3',
            'username' => 'required|min:3|unique:users|without_spaces',
            'password' => 'required|min:5',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i'
        ], [
            'without_spaces' => 'The username cannot contain space'
        ]);
        $validateddata['password'] = bcrypt($request->password);
        $validateddata['picture'] = 'avatars-' . mt_rand(1, 8) . '.png';

        User::create($validateddata);

        $activity = [
            'user_id' => Auth::id(),
            'action' => 'added a new employee -' . strtolower($request->username)
        ];
        ActivityLog::create($activity);

        return redirect('/user')->with('success', 'New employee has been added !');
    }

    public function show(User $user)
    {
        if (!$user->hasRole('owner')) {
            return redirect()->back();
        }

        return view('account.index', [
            'user' => $user
        ]);

    }

    public function edit(User $user)
    {
        if (!$user->hasRole('owner')) {
            return redirect()->back();
        }

        return view('user.edit', [
            'users' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        Validator::extend('without_spaces', function ($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });

        $validateddata = $request->validate([
            'level_id' => 'required',
            'name' => 'required|min:3',
            'username' => 'required|min:3|unique:users,username,' . $user->id . '|without_spaces|',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i'
        ], [
            'without_spaces' => 'The username cannot contain space'
        ]);

        User::where('id', $user->id)
            ->update($validateddata);

        $activity = [
            'user_id' => Auth::id(),
            'action' => 'edited the employee -' . strtolower($request->username)
        ];
        ActivityLog::create($activity);
        return redirect('/user')->with('success', 'employee has been updated !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        $user_id = $request->users;
        for ($i = 0; $i < count($user_id); $i++) {
            $username = $user->find($user_id[$i])->username;
            $activity = [
                'user_id' => Auth::id(),
                'action' => 'deleted the employee -' . strtolower($username)
            ];
            Storage::delete($user_id[$i]);
            $user->destroy($user_id[$i]);
            ActivityLog::create($activity);
        };
        return redirect('/user');
    }

    public function updateProfile(Request $request, User $user)
    {
        Validator::extend('without_spaces', function ($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });

        $validateddata = $request->validate([
            'name' => 'required|min:3',
            'username' => 'required|min:3|unique:users,username,' . $user->id . '|without_spaces|',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'picture' => 'image|file|max:2048'
        ], [
            'without_spaces' => 'The username cannot contain space'
        ]);

        if ($request->file('picture')) {
            if (strpos($user->picture, 'avatars') === false) {
                $old_pict = 'profile/' . $user->picture;
                Storage::delete($old_pict);
            }
            $file = $request->file('picture');
            $fileName = explode('/', $file->store('profile'))[1];
            $validateddata['picture'] = $fileName;
        }

        User::where('id', $user->id)
            ->update($validateddata);

        return redirect('/user/' . $user->id);
    }
}
