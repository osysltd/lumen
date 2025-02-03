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
use Illuminate\Validation\Rule;
use App\Http\EmailVerificationRequest;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    public function __construct()
    {

        $this->middleware('guest', [
            'only' => [
                'login',
                'doLogin',
                'register',
                'doRegister',
                'reset',
                'doReset'
            ]
        ]);

        $this->middleware('auth:web', [
            'only' => [
                'home',
                'profile',
                'doProfile',
                'notice',
                'verify',
                'doSend'
            ]
        ]);

        $this->middleware('signed', ['only' => 'verify']);
        $this->middleware('throttle:6,1', ['only' => ['verify', 'send', 'notice', 'doProfile']]);

    }

    public function login()
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
            if (Auth::attempt($credentials, (bool) $request->only('remember'))) {
                Session::flash('message', 'You have signed in successfully!');
                return redirect()->route('home');
            }

        } catch (\Illuminate\Validation\ValidationException $th) {
        } finally {
            Session::flash('message', 'Email address or password is incorrect!');
            return redirect()->route('login');
        }
    }


    public function register()
    {
        return view('auth.register');
    }


    public function doRegister(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]
            );

            $data = $request->all();
            $user = $this->create($data);
            Auth::login($user, true);
            event(new Registered($user));
            $request->user()->sendEmailVerificationNotification();
            Session::flash('message', 'You have been registered successfully!');
            return redirect()->route('verification.notice');

        } catch (\Illuminate\Validation\ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
            return redirect()->route('register');
        }
    }


    public function reset()
    {
        return view('auth.reset');
    }


    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function doReset(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['min:6', 'required', 'confirmed', Rules\Password::defaults()],
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
            if (Auth::user()->hasVerifiedEmail()) {
                return view(view: 'home');
            } else {
                return redirect()->route('profile');
            }
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
    }


    public function profile(Request $request)
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            Auth::setUser($user);
            return view('auth.profile', ['user' => $user]);
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
    }


    public function doProfile(Request $request)
    {
        if (Auth::check()) {
            try {
                $this->validate(
                    $request,
                    [
                        'name' => 'required',
                        'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::user()->id, 'id')],
                        'password' => ['nullable', 'required_with:password_confirmation', 'same:password_confirmation', Rules\Password::defaults()],
                        'password_confirmation' => ['nullable', Rules\Password::defaults()]
                    ]
                );

                $user = User::find(Auth::user()->id);
                $user->update(collect($request->all())->filter()->all());

                if ($user->getChanges('email')) {
                    $request->user()->sendEmailVerificationNotification();
                }

                $user->save();
                Auth::setUser($user);

                Session::flash('message', 'Your profile has been updated successfully!');
                return redirect()->route('profile');

            } catch (\Illuminate\Validation\ValidationException $th) {
                Session::flash('message', json_encode($th->errors()));
                return redirect()->route('profile');
            }
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
    }


    public function logout()
    {
        //Session::flush();
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        Session::flash('message', 'You have signed out successfully!');

        return redirect()->route('login');
    }

    /**
     * Display an email verification notice.
     *
     * @return \Illuminate\Http\Response
     */
    public function notice(Request $request)
    {
        if (Auth::check()) {
            return $request->user()->hasVerifiedEmail()
                ? redirect()->route('home') : view('auth.profile');
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');

    }

    /**
     * User's email verificaiton.
     *
     * @param  \App\Http\EmailVerificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function verify(EmailVerificationRequest $request)
    {
        if (Auth::check()) {
            $request->fulfill();
            Session::flash('message', 'Your email address has been verified successfully!');
            return redirect()->route('home');
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
    }

    /**
     * Resent verificaiton email to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doSend(Request $request)
    {
        if (Auth::check()) {
            $request->user()->sendEmailVerificationNotification();
            Session::flash('message', 'A fresh verification link has been sent to your email address.');
            return redirect()->route('profile');
        }
        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');

    }
}