<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginate = Auth::user()->paginate;
        $isAdmin = Auth::user()->admin;
        $users = User::orderBy('created_at','desc')->paginate($paginate);
        return view('users.index',compact('users','isAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $userRequest
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $userRequest)
    {
        $validated = $userRequest->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request = $userRequest->except('_token');
        $isAdmin = !empty($request['admin']) ? 1 : 0;
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'admin' => $isAdmin
        ]);

        return $userRequest->wantsJson() ? $this->sendResponse($user, 'User created successfully') : Redirect::route('users.index')->with(['message' => 'User created successfully', 'type' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,$user->id",
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return $request->wantsJson() ? $this->sendResponse($user, 'User updated successfully') : Redirect::route('users.index')->with(['message' => 'User updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return $request->wantsJson() ? $this->sendResponse(null, 'User deleted successfully') : Redirect::route('users.index')->with(['message' => 'User deleted successfully', 'type' => 'success']);
    }

    /**
     * send empty user object.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNewUser()
    {
        $user = new User();
        return view('users.form',compact('user'));
    }

    /**
     * get individual post data from given id..
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function getUserData(User $user)
    {
        return view('users.form',compact('user'));
    }

    /**
     * give selected users admin privileges.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function makeUserAdmin(User $user)
    {
        $user->admin = !$user->admin;
        $message = 'User is no longer a admin';
        if ($user->admin) {
            $message = 'User now have admin privileges';
        }
        $user->save();

        return Redirect::route('users.index')->with(['message' => $message, 'type' => 'success']);
    }

    /**
     * block selected users to prevent spamming.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function blockUser(User $user)
    {
        $user->blocked = !$user->blocked;
        $message = 'User activated';
        if ($user->blocked) {
            $message = 'User blocked';
        }
        $user->save();

        return Redirect::route('users.index')->with(['message' => $message, 'type' => 'success']);
    }
}
