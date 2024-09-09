<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserData;
use Illuminate\Support\Carbon;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\BadRequestException;
use App\Exceptions\InvalidCredentialException;

class AuthController extends Controller
{
    /**
     * Create and return token once password is verified.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password))
        {
            throw new InvalidCredentialException();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully login',
            'token' => $user->createToken($user->id)->plainTextToken,
        ]);
    }

    /**
     * Remove token from database.
     */
    public function logout()
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Return user data.
     */
    public function me()
    {
        return response()->json(UserData::from(Auth::user()));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        DB::transaction(function () use ($request)
        {

            $user = Auth::user();

            $user->password = Hash::make($request->password);
            $user->save();
        });
    }

    /**
     * Update user data.
     *
     * @param Request $request
     * @return void
     */
    public function updateMe(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        DB::transaction(function () use ($request)
        {
            $user = Auth::user();

            $email = $request->email;
            $name = $request->name;

            if (User::where('email', $email)->where('id', '!=', $user->id)->first() != null)
            {
                throw new BadRequestException('Duplicate emails were found.');
            }

            $user->name = $name;
            $user->email = $email;
            $user->save();
        });
    }

    public function requestResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $passwordReset = null;
        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            throw new BadRequestException('Unable to find email.');
        }

        DB::transaction(function () use ($request, &$passwordReset)
        {
            $passwordReset = PasswordResetRequest::create([
                'email' => $request->email,
                'expired_at' => Carbon::now()->addHour()
            ]);
        });

        if ($passwordReset)
        {
            Mail::to($user->email)
                ->send(new ResetPasswordMail(
                    $user->email,
                    $passwordReset->id,
                ));
        }
    }

    public function checkResetPassword(PasswordResetRequest $model, Request $request)
    {
        if ($request->email && $model->email == $request->email)
        {
            return response()->json([
                'status' => 'success'
            ]);
        }

        return response()->json([
            'status' => 'fail'
        ]);
    }

    public function processResetPassword(PasswordResetRequest $model, Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        DB::transaction(function () use ($model, $request)
        {
            if ($request->email && $model->email == $request->email)
            {
                if (Carbon::now()->diffInDays($model->expired_at, false) < 0)
                {
                    throw new BadRequestException('The request already expired.');
                }

                $user = User::where('email', $model->email)->firstOrFail();
                $user->password = Hash::make($request->password);
                $user->save();

                $model->delete();
            }
        });
    }
}
