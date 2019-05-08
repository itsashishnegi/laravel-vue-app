<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Api\UsersController@login');
Route::post('register', 'Api\UsersController@register');
Route::put('verify/{uuid}', 'Api\UsersController@verify');
Route::post('password/email', 'Api\ForgotPasswordController@sendResetLinkOnEmail');

Route::post('messages/receive', 'Api\MessagesController@receive')->name('sms_default');
Route::post('messages/callback', 'Api\MessagesController@callback')->name('sms');
Route::post('calls/voice', 'Api\CallsController@voice')->name('voice_default');
Route::post('calls/callback', 'Api\CallsController@callback')->name('callback');
Route::post('calls/voicemail', 'Api\CallsController@voicemailCallback')->name('voicemail');
Route::post('calls/pre_call_announcement', 'Api\CallsController@preCallAnnouncementCallback')->name('pre_call_announcement');
Route::post('calls/gather', 'Api\CallsController@gatherCallback')->name('gather');

Route::group(['middleware' => 'auth:api'], function(){
	Route::get('logout', 'Api\UsersController@logout');
	
	Route::get('users', 'Api\UsersController@index');
	Route::put('users', 'Api\UsersController@update');
	Route::delete('users', 'Api\UsersController@destroy');
	Route::post('users/credit', 'Api\UsersController@credit');
	Route::get('twilio/token', 'Api\UsersController@twilioAccessToken');

	Route::put('users/password', 'Api\UsersController@change_password');
	Route::get('users/settings', 'Api\UserSettingsController@index');
	Route::put('users/settings', 'Api\UserSettingsController@update');
	Route::get('settings/forwarding', 'Api\UserSettingsController@forwardingNumber');
	Route::post('settings/forwarding', 'Api\UserSettingsController@saveForwardingNumber');


	Route::get('providers', 'Api\ProvidersController@index');

	Route::get('packages/{iso}', 'Api\PackagesController@index');
	
	Route::get('contacts', 'Api\UserContactsController@index');
	Route::post('contacts', 'Api\UserContactsController@store');
	Route::get('contacts/{uuid}', 'Api\UserContactsController@show');
	Route::put('contacts/{uuid}', 'Api\UserContactsController@update');
	Route::delete('contacts/{uuid}', 'Api\UserContactsController@destroy');
	Route::get('contacts/download/{uuid}', 'Api\UserContactsController@download');

	Route::get('blocked', 'Api\BlockedContactsController@index');
	Route::post('blocked', 'Api\BlockedContactsController@store');
	Route::delete('blocked/{uuid}', 'Api\BlockedContactsController@destroy');

	Route::get('numbers/available', 'Api\AvailablePhoneNumbersController@index');
	Route::post('numbers/available', 'Api\AvailablePhoneNumbersController@store');
	Route::get('numbers/countries', 'Api\AvailablePhoneNumbersController@countries');
	Route::get('numbers/regions', 'Api\AvailablePhoneNumbersController@regions');

	Route::get('numbers', 'Api\PhoneNumbersController@index');
	Route::get('numbers/{uuid}', 'Api\PhoneNumbersController@show');
	Route::put('numbers/{uuid}', 'Api\PhoneNumbersController@update');
	Route::put('numbers/{uuid}/extend', 'Api\PhoneNumbersController@extend');
	Route::delete('numbers/{uuid}', 'Api\PhoneNumbersController@destroy');
	Route::post('numbers/settings', 'Api\PhoneNumbersController@settings');

	Route::get('numbers/{uuid}/greetings', 'Api\PhoneNumberGreetingsController@index');
	Route::post('greetings', 'Api\PhoneNumberGreetingsController@store');
	Route::put('greetings/{uuid}', 'Api\PhoneNumberGreetingsController@update');
	Route::delete('greetings/{uuid}', 'Api\PhoneNumberGreetingsController@destroy');
	Route::get('greetings/{uuid}', 'Api\PhoneNumberGreetingsController@download');

	Route::get('logs', 'Api\LogsController@index');
	Route::delete('logs/thread/{uuid}', 'Api\LogsController@deleteThread');
	Route::delete('logs/{uuid}', 'Api\LogsController@destroy');
	Route::delete('logs', 'Api\LogsController@delete');

	Route::post('messages', 'Api\MessagesController@store');
	Route::get('messages/{uuid}', 'Api\MessagesController@download');

	Route::get('pricing', 'Api\PricingController@index');

	Route::post('redeem', 'Api\UserReferralsController@store');

	Route::get('voicemails/{uuid}', 'Api\VoicemailsController@download');

	Route::get('addresses', 'Api\UserAddressesController@index');
	Route::post('addresses', 'Api\UserAddressesController@store');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/download', function () {
    if (empty(request('path'))) {
        return response()->json(404);
    }
    return Storage::download(request('path'));
})->name('download')->middleware('signed');

Route::get('/mailable', function () {

    $user = App\User::find(1);

    return new App\Mail\RegistrationEmailVerificationCode($user);
});
