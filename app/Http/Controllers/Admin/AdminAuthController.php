<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function loginView()
    {
        try {
            return view('admin.auth.login');
        } catch (Exception $e) {
            Log::error('Failed to open login page: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function loginSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                if ($user->account_status == 0) {
                    Auth::logout();

                    return response()->json([
                        'status' => false,
                        'message' => 'Your account is deactivated. Please contact admin.'
                    ], 403);
                }

                if ($user->role_id == 1) {
                    return response()->json([
                        'status' => true,
                        'redirect' => route('admin.dashboard')
                    ]);
                }

                Auth::logout();

                return response()->json([
                    'status' => false,
                    'message' => 'Login failed. Please enter valid credentials and try again.'
                ], 401);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login failed. Please enter valid credentials and try again.'
            ], 401);

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong! Please try again.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login.view')->with('success', 'Logged out successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Logout failed. Try again.');
        }
    }

    public function forgotPasswordView()
    {
        try {
            return view('admin.auth.forgotPassword');
        } catch (Exception $e) {
            Log::error('Failed to open forget page: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function forgotPasswordSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $user = User::where('email', $request->email)
                ->where('role_id', 1)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => "We couldn't find an Admin account associated with this email address."
                ], 404);
            }

            $token = Str::random(64);

            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // Mail Send
            try {

                Mail::send('email.resetPassword', [
                    'token' => $token,
                    'name' => $user->name,
                    'email' => $user->email
                ], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Admin Password Reset Request');
                });

                return response()->json([
                    'status' => true,
                    'message' => 'A password reset link has been sent to your registered email address. Please check your inbox or spam folder.'
                ]);

            } catch (\Exception $e) {

                Log::error('Forgot Password Mail Error', [
                    'email' => $user->email,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Unable to send password reset email. Please try again later.'
                ], 500);
            }

        } catch (\Exception $e) {

            Log::error('Forgot Password Error', [
                'email' => $request->email,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function resetPasswordView($token)
    {
        $resetPassword = DB::table('password_resets')->where('token', $token)->first();

        if (!$resetPassword) {

            return redirect()->route('admin.forgot.password.view')->with('error', 'Invalid token. Please try again.');
        }
        return view('admin.auth.resetPassword', ['token' => $token]);

    }

    public function resetPasswordSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'New Password field is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
            'password_confirmation.required' => 'Confirm Password field is required.',
            'password_confirmation.same' => 'New Password and Confirm Password must be same.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $reset = DB::table('password_resets')
                ->where('token', $request->token)
                ->first();

            if (!$reset) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired reset link.'
                ], 404);
            }

            $user = User::where('email', $reset->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_resets')
                ->where('token', $request->token)
                ->delete();
session()->flash('success', 'Password updated successfully.');
            return response()->json([
                'status' => true,
                'redirect' => route('admin.login.view')
            ]);

        } catch (Exception $e) {

            Log::error('Reset Password Error', [
                'token' => $request->token,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Unable to reset password. Please try again.'
            ], 500);
        }
    }
}
