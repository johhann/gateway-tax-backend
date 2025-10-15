<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NewPasswordController extends Controller
{
    public function handle(ResetPasswordRequest $request)
    {
        abort_if(
            ! Hash::check($request->old_password, Auth::user()->password),
            403,
            'Incorrect current password'
        );

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        if ($request->log_out_other_devices == true) {
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        }

        return [
            'message' => 'Password set successfully',
        ];
    }
}
