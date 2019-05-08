<?php

namespace App\Http\Controllers\Api;

use Twilio;
use App\User;
use App\Package;
use App\Receipt;
use ReceiptValidator;
use App\UserTransaction;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Jobs\UpdatePhoneNumberUrl;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegistrationEmailVerificationCode;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register()
    {

        $this->validate(request(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:255|confirmed',
            'phone_number' => 'required|string|max:255',
            'verification_method' => [
                'required',
                Rule::in(['email', 'sms']),
            ],
            'device_type' => [
                'required',
                Rule::in(['ios', 'android']),
            ]
        ]);

        $user = new User([
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'old_password' => Hash::make(request('password')),
            'phone_number' => request('phone_number'),
            'uuid' => (string) Str::uuid(),
            'verification_code' => substr(number_format(time() * rand(),0,'',''),0,4),
            'referral_code' => NULL,
            'device_type' => request('device_type')
        ]);

        $user->save();

        return response()->json([
            'uuid' => $user->uuid
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login()
    {
        $this->validate(request(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = array_merge(
            request(['email', 'password']), 
            [
                'is_active' => true,
                'is_verified' => true,
                'is_deleted' => false
            ]
        );

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = Auth::user();

        $user->revokeAccessTokens();

        $tokenResult = $user->createAccessToken();
        return response()->json($tokenResult);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout()
    {
        $user = Auth::user();

        $user->revokeAccessTokens();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Verify user and create token
     *
     * @param  [string] uuid
     * @param  [string] verification_code
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function verify($uuid)
    {
        $this->validate(request(), [
            'verification_code' => 'required|string'
        ]);

        $user = User::where('uuid', '=', $uuid)
                        ->where('verification_code', '=', request('verification_code'))
                        ->where('is_verified', '=', false)
                        ->firstOrFail();

        $user->is_active = true;
        $user->is_verified = true;
        $user->is_deleted = false;
        $user->verification_code = NULL;
        $user->save();

        $user->revokeAccessTokens();

        $tokenResult = $user->createAccessToken();
        return response()->json($tokenResult);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function index()
    {
        return response()->json(Auth::user());
    }

    /**
     * Update user details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        $this->validate(request(), [
            'name' => 'required_without_all:age,gender,device_token,device_type|string|max:255',
            'age' => 'required_without_all:name,gender,device_token,device_type|integer',
            'gender' => [
                'required_without_all:age,name,device_token,device_type',
                Rule::in(['male', 'female', 'others']),
            ],
            'device_token' => 'required_without_all:age,gender,name|string',
            'device_type' => [
                'required_with:device_token',
                Rule::in(['ios', 'android']),
            ]
        ]);

        $user = Auth::user();

        $user->name = request('name') ?? $user->name;
        $user->age = request('age') ?? $user->age;
        $user->gender = request('gender') ?? $user->gender;
        $user->device_token = request('device_token') ?? $user->device_token;
        $user->device_type = request('device_type') ?? $user->device_type;
        $user->save();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $user = Auth::user();

        $user->is_deleted = true;
        $user->save();

        $user->revokeAccessTokens();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    /**
     * Change user password
     *
     * @param  [string] old_password
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return \Illuminate\Http\JsonResponse
     */
    public function change_password()
    {
        $this->validate(request(), [
            'old_password' => 'required|string|min:6|max:255',
            'password' => 'required|string|min:6|max:255|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check(request('old_password'), $user->password))
            return response()->json([
                'message' => 'Old password does not match.'
            ], 401);

        $user->password = Hash::make(request('password'));
        $user->save();

        return response()->json([
            'message' => 'OK'
        ]);
    }

    /**
    * Function to generate access token for client device
    */
    public function twilioAccessToken() {

        // required params
        $this->validate(request(), [
            'device_type' => [
                'required',
                Rule::in(['android', 'ios']),
            ]
        ]);

        return response()->json([
            'access_token' => Twilio::accessToken()
        ]);
    }

    public function credit() {

        $this->validate(request(), [
            'package_id' => 'required',
            'receipt' => 'required|string',
            'device_type' => [
                'required',
                Rule::in(['ios', 'android']),
            ],
        ]);

        $requestData = request()->all();

        // find package
        $package = Package::where('id', $requestData['package_id'])
                    ->where('is_active', true)
                    ->where('type', 'recharge')
                    ->with('product')
                    ->firstOrFail();

        $packageCost = $package->cost;
        $currentUser = auth()->user();

        // check receipt existence
        $receipt = Receipt::where('receipt', $requestData['receipt'])
                        ->where('device_type', $requestData['device_type'])
                        ->count();
        if ($receipt != 0) {
            return response()->json(["message" => "Reciept already exists."], 400);
        }
        // validate receipt
        $validate = ReceiptValidator::{$requestData['device_type']}($requestData['receipt'], $package->product->name);

        if ($validate !== true) {
            return response()->json(["message" => $validate], 400);
        }

        // save receipt
        $newReceipt = new Receipt();
        $newReceipt->user_id = $currentUser->id;
        $newReceipt->device_type = $requestData['device_type'];
        $newReceipt->receipt = $requestData['receipt'];
        $newReceipt->save();

        // add it in user balance
        $currentUser->credit += $packageCost;
        $currentUser->save();

        // save transaction
        $transaction = new UserTransaction();
        $transaction->uuid = (string) Str::uuid();
        $transaction->user_id = $currentUser->id;
        $transaction->amount = $packageCost;
        $transaction->is_credit = true;
        $transaction->type = config("loudcloud.transaction_type.credit.add");
        $transaction->save();

        UpdatePhoneNumberUrl::dispatch(auth()->user()->id);

        return response()->json([
            'credit' => $currentUser->credit
        ]);
    }
}
