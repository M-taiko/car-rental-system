<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('profile_photo')) {
                // Get old photo path from database
                $oldPhoto = Auth::user()->profile_photo_path;

                // Delete old photo if exists
                if ($oldPhoto && Storage::disk('public')->exists('profile-photos/' . $oldPhoto)) {
                    Storage::disk('public')->delete('profile-photos/' . $oldPhoto);
                }

                // Store new photo
                $filename = time() . '_' . $request->file('profile_photo')->getClientOriginalName();
                $path = 'profile-photos/' . $filename;
                
                // Store file using Storage facade
                $request->file('profile_photo')->storeAs('public/profile-photos', $filename);
                
                // Update user with only the filename
                Auth::user()->update([
                    'profile_photo_path' => $filename
                ]);
            }

            return redirect()->back()->with('success', __('messages.profile_photo_updated'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.profile_photo_upload_error'));
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return redirect()->back()->withErrors(['current_password' => __('messages.invalid_current_password')]);
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', __('messages.password_updated'));
    }
}
