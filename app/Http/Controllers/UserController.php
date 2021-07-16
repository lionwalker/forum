<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $userRequest)
    {
        $validated = $userRequest->except('_token');
        $isAdmin = !empty($validated['admin']) ? 1 : 0;
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'admin' => $isAdmin
        ]);

        $token = $user->createToken('forum-app')->plainTextToken;

        return $userRequest->wantsJson() ? $this->sendResponse($user, 'User created successfully', $token) : Redirect::route('users.index')->with(['message' => 'User created successfully', 'type' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,$user->id",
        ]);

        $isAdmin = !empty($validated['admin']) ? 1 : 0;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->admin = $isAdmin;
        $user->save();

        return $request->wantsJson() ? $this->sendResponse($user, 'User updated successfully') : Redirect::route('users.index')->with(['message' => 'User updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return $request->wantsJson() ? $this->sendResponse(null, 'User deleted successfully') : Redirect::route('users.index')->with(['message' => 'User deleted successfully', 'type' => 'success']);
    }

    /**
     * send empty user object.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getUserData(User $user)
    {
        return view('users.form',compact('user'));
    }

    /**
     * give selected users admin privileges.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Http\RedirectResponse
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

    /**
     *  Return logged in user data.
     *
     * @return array[]
     */
    public function profile()
    {
        $user = Auth::user();
        return [
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ];
    }

    /**
     * Login API users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        // Check email
        $user = User::whereEmail($validated['email'])->first();

        // Check password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->sendError(null, 'Logging in unsuccessful');
        }

        $token = $user->createToken('forum-app')->plainTextToken;

        return $this->sendResponse(null, 'Logged in successfully', $token);
    }

    /**
     * Logout API users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return $this->sendResponse(null, 'Logged out successfully');
    }
}
