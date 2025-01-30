<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AuthController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }


    public function doLogin(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'email' => 'required',
                    'password' => 'required',
                ]
            );

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                Session::flash('message', 'You have signed in successfully');
                return redirect()->route('home');
            }

        } catch (\Illuminate\Validation\ValidationException $th) {
        } finally {
            Session::flash('message', 'Email address or password is incorrect');
            return redirect()->route('login');

        }
    }


    public function register()
    {
        return view('auth.register');
    }


    public function doRegistration(\Illuminate\Http\Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => ['required', 'confirmed'],
                ]
            );

            Session::flash('message', 'You have registered successfully');
            $data = $request->all();
            $user = $this->create($data);
            Auth::login($user);

            return redirect()->route('home');

        } catch (\Illuminate\Validation\ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
            return redirect()->route('register');
        }
    }


    public function recovery()
    {
        return view('auth.recovery');
    }


    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function doRecovery(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }


    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }


    public function home()
    {
        if (Auth::check()) {
            return view('home');
        }
        Session::flash('message', 'You are not allowed to access');

        return redirect('login');
    }

    public function profile()
    {
        if (Auth::check()) {
            return view('profile.edit');
        }
        Session::flash('message', 'You are not allowed to access');

        return redirect('login');
    }


    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}