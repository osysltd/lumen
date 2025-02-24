<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use App\Http\EmailVerificationRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use \Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\ResetsPasswords;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->broker = 'users';

        $this->middleware('guest', [
            'only' => [
                'login',
                'doLogin',
                'register',
                'doRegister',
                'sendResetLinkEmail',
                'showResetForm',
                'reset'
            ]
        ]);

        $this->middleware('auth:web', [
            'only' => [
                'home',
                'profile',
                'doProfile',
                'notice',
                'verify',
            ]
        ]);

        $this->middleware('signed', ['only' => ['verify']]);
        $this->middleware('throttle:6,1', [
            'only' => [
                'verify',
                'resend',
                'notice',
                'doProfile',
            ]
        ]);

    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


    /**
     * Create new User Model
     *
     * @return\App\Models\User
     */
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function home(Request $request)
    {
        if (Auth::check()) {
            if ($request->user()->hasVerifiedEmail()) {
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
            $user = User::find($request->user()->id);
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
                        'email' => ['required', 'email', Rule::unique('users')->ignore($request->user()->id, 'id')],
                        'password' => ['nullable', 'required_with:password_confirmation', 'same:password_confirmation', Rules\Password::defaults()],
                        'password_confirmation' => ['nullable', Rules\Password::defaults()]
                    ]
                );

                $user = User::find($request->user()->id);
                $user->update(collect($request->all())->filter()->all());

                if ($user->getChanges('email')) {
                    $request->user()->sendEmailVerificationNotification();
                }

                $user->save();
                Auth::setUser($user);

                Session::flash('message', 'Your profile has been updated successfully!');
                return redirect()->route('profile');

            } catch (ValidationException $th) {
                Session::flash('message', json_encode($th->errors()));
                return redirect()->route('profile');
            }
        }

        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
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
            return redirect()->route('profile');

        } catch (ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
            return redirect()->route('register');
        }
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

        } catch (ValidationException $th) {
        } finally {
            Session::flash('message', 'Email address or password is incorrect!');
            return redirect()->route('login');
        }
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
                ? redirect()->route('home') : view('auth.profile', ['user' => $request->user()]);
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
    public function resend(Request $request)
    {
        if (Auth::check()) {
            $request->user()->sendEmailVerificationNotification();
            Session::flash('message', 'A fresh verification link has been sent to your email address.');
            return redirect()->route('profile');
        }

        Session::flash('message', 'You are not allowed to access this page!');
        return redirect()->route('login');
    }


    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset')->with(['token' => $token, 'email' => decrypt($request->email)]);
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.recover');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {

            $this->validate($request, ['email' => 'required|email']);

            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentialsEmail($request)
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);

        } catch (ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
            return redirect()->route('password.request');
        }

    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentialsEmail(Request $request)
    {
        return $request->only('email');
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        Session::flash('message', trans($response));

        return redirect()->to('/');
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\ResposeFactory
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        Session::flash('message', trans($response));

        return redirect()->route('password.request');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        try {
            $this->validate($request, [
                'token' => 'required',
                'email' => 'required|email',
                'password' => ['required', 'required_with:password_confirmation', 'same:password_confirmation', Rules\Password::defaults()],
                'password_confirmation' => ['required', Rules\Password::defaults()]
            ]);

            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $response = $this->broker()->reset(
                $this->credentialsReset($request),
                function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            return $response == Password::PASSWORD_RESET
                ? $this->sendResetResponse($request, $response)
                : $this->sendResetFailedResponse($request, $response);


        } catch (ValidationException $th) {
            Session::flash('message', json_encode($th->errors()));
            return redirect()->route('password.reset', ['token' => $request->token, 'email' => encrypt($request->email)]);
        }

    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentialsReset(Request $request)
    {
        return $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword(User $user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        Session::flash('message', trans($response));

        return redirect()->route('profile');
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        Session::flash('message', trans($response));

        return redirect()->to('/');
    }

}