<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    public function edit()
    {
        try {

            $pageTitle = 'Admin Profile';

            return view('admin.profile.profile', compact('pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load profile page.');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:max_width=200,max_height=200',
        ];



        $validator = Validator::make(
            $request->all(),
            $rules,
            [
                'name.required' => 'Name field is required.',
                'name.regex' => 'Name must contain letters and spaces only.',
                'email.required' => 'Email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already registered.',

                'image.dimensions' => "Image size can't exceeds the size 200px X 200px",
                'image.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
            ]
        );



        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Update basic details
            $user->name = $request->name;
            $user->email = $request->email;


            // Handle image upload
            if ($request->hasFile('image')) {

                $imageName = time() . '.' . $request->image->getClientOriginalExtension();

                $request->image->move(
                    public_path('admin_assets/images/'),
                    $imageName
                );

                // Delete old image after new image is uploaded successfully
                if ($user->image && file_exists(public_path('admin_assets/images/' . $user->image))) {
                    unlink(public_path('admin_assets/images/' . $user->image));
                }

                $user->image = $imageName;
            }

            $user->save();

            session()->flash('success', 'Profile updated successfully.');

            // return redirect()->back()->with('success', 'Profile updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully.',
                'redirect' => route('admin.profile')
            ]);

        } catch (Exception $e) {
            Log::error('Admin Profile Update Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to update profile. Try again.'
            ], 500);
        }
    }

    public function checkEmail(Request $request)
    {
        try {
            $email = $request->input('email');
            $exists = User::where('email', $email)->where('id', '!=', Auth::id())->exists();

            return response()->json([
                'exists' => $exists
            ]);

        } catch (Exception $e) {
            Log::error('Check admin Email Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to check admin email.'
            ], 500);
        }
    }

    public function editpassword()
    {
        try {

            $pageTitle = 'Admin Update Password';

            return view('admin.profile.updatePassword', compact('pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load password update page.');
        }
    }

    public function updatepassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'old_password' => 'required',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',      // At least one lowercase letter
                    'regex:/[A-Z]/',      // At least one uppercase letter
                    'regex:/[0-9]/',      // At least one number
                    'regex:/[@$!%*#?&]/', // At least one special character
                ],
                'password_confirmation' => 'required|same:password',
            ],
            [
                'old_password.required' => 'Current password field is required.',
                'password.required' => 'New password field is required.',
                'password.min' => 'New password must be at least 8 characters.',
                'password_confirmation.required' => 'Confirm password field is required.',
                'password_confirmation.same' => 'New Password and Confirm Password must be same.',
                'password.regex' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]
        );

        if ($validator->fails()) {
            // return back()
            //     ->withErrors($validator)
            //     ->withInput();
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Check old password
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Current password is incorrect.'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'Password updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully.',
                'redirect' => route('admin.password')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to update password. Try again.'
            ], 500);
        }
    }


}
