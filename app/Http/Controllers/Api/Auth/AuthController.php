<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;

class AuthController extends Controller
{
    /**
     * Create user with basic details
     *
     * @param  [string] firstname
     * @param  [string] lastname
     * @param  [string] email
     * @param  [string] mobile
     * @param  [string] gender
     * @param  [string] address
     * @param  [string] city
     * @param  [string] state
     * @param  [string] country
     * @param  [integer] pincode
     * @param  [date] birthdate
     * @param  [string] password
     * @param  [string] confirm_password
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::firstOrCreate([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'pincode' => $request->pincode,
                'birthdate' => date('Y-m-d', strtotime($request->birthdate)),
                'password' => Hash::make($request->password)
            ]);

            if ($user) {
                // SANCTUM:ABILITY
                $abilities = [
                    'user:create', // user create permission
                    'user:edit', // user edit permission
                    'user:delete', // user delete permission
                    'user:show', // user show permission
                    'user:list', // user list permission
                    'user:search' // user search permission
                ];
                $expiresAt = Carbon::now()->addMinutes(20); // after 20 minutes of login
                $token = $user->createToken(config('sanctum.token_sanctum'), $abilities, $expiresAt)->plainTextToken; // SANCTUM:ABILITY
                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'message' => 'User created Successfully...!',
                    'accessToken' => $token,
                    'expires_at' => $expiresAt,
                ], 201);
            }

            throw new \Exception('fails not created...!', 403);

        } catch (\Illuminate\Database\QueryException $ex) { // Handle query exception
            return response()->json(['status' => false, 'data' => [], 'message' => "Error Query inserting data : " . $ex->getMessage()], 400);
        } catch (\Exception $ex) { // general exception
            return response()->json(['status' => false, 'data' => [], 'message' => $ex->getMessage()], 404);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'mobile', 'password');
        if (auth()->attempt($credentials)) {
            $user = $request->user();
            if ($user->tokens()->where('tokenable_id', $user->id)->exists()) { // delete token if already login exists
                $user->tokens()->delete();
            }
            // SANCTUM:ABILITY
            $abilities = [
                'user:create', // user create permission
                'user:edit', // user edit permission
                'user:delete', // user delete permission
                'user:show', // user show permission
                'user:list', // user list permission
                'user:search' // user search permission
            ];
            $expiresAt = Carbon::now()->addMinutes(20); // after 20 minutes of login
            $token = $user->createToken(config('sanctum.token_sanctum'), $abilities, $expiresAt)->plainTextToken; // SANCTUM:ABILITY

            return response()->json([
                'status' => true,
                'message' => 'User login successful',
                'data' => $user,
                'token_type' => 'Bearer',
                'accessToken' => $token,
                'expires_at' => $expiresAt,
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorized, Credentials are not valid...!', 'data' => []], 401);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
            'data' => []
        ]);
    }

    /**
     * Refesh user token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $user = auth()->user();
        $user->tokens()->delete(); // Revoke all of the user's existing tokens       
        $token = $user->createToken(config('sanctum.token_sanctum'))->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'User Token refreshed Successfully...!',
            'data' => $user,
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Create user with basic details
     *
     * @param  [string] email
     * @param  [string] mobile
     * @param  [string] old_password
     * @param  [string] new_password
     * @param  [string] confirm_new_password
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)
                ->where('mobile', $request->mobile)
                ->first();
            if (!empty($user)) {
                if (Hash::check($request->old_password, $user->password)) { // old password matched                    
                    if (!Hash::check($request->old_password, Hash::make($request->new_password))) { // new password and old password do not match 
                        $user->fill([
                            'password' => Hash::make($request->new_password)
                        ])->save();
                        $user->tokens()->delete(); // Revoke all of the user's existing tokens   
                        return response()->json([
                            'status' => true,
                            'message' => 'Password Changed successfully...!',
                            'data' => []
                        ], 201);
                    }
                    throw new \Exception('New Password must be different from Old Password...!', 403);
                }
                throw new \Exception('Old Password does not match...!', 403);
            }
            throw new \Exception('Email Address and Mobile number not found in our database...!', 403);

        } catch (\Illuminate\Database\QueryException $ex) { // Handle query exception
            return response()->json(['status' => false, 'message' => "Error Query inserting data : " . $ex->getMessage(), 'data' => []], 400);
        } catch (\Exception $ex) { // general exception
            return response()->json(['status' => false, 'message' => $ex->getMessage(), 'data' => []], 404);
        }
    }
}
