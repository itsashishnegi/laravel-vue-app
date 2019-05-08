<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'old_password', 'uuid', 'phone_number', 'verification_code', 'device_type', 'referral_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the setting.
     */
    public function setting()
    {
        return $this->hasMany('App\UserSetting');
    }

    /**
     * Get the forwarding_numbers.
     */
    public function forwarding_numbers()
    {
        return $this->hasMany('App\UserForwardingNumber')->orderBy('priority');
    }

    /**
     * Get the user_transactions.
     */
    public function user_transactions()
    {
        return $this->hasMany('App\UserTransaction');
    }

    /**
     * Revoke all access_tokens assigned to user.
     */
    public function revokeAccessTokens()
    {
        $userTokens = $this->tokens;
        foreach($userTokens as $token) {
            $token->revoke();   
        }
        return true;
    }

    /**
     * Create new access_token for user.
     */
    public function createAccessToken()
    {
        $tokenResult = $this->createToken('API Access Token');
        $token = $tokenResult->token;
        $token->save();
        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
    }

    /**
     * Set the referral_code.
     *
     * @param  string $value
     * @return void
     */
    public function setReferralCodeAttribute($value)
    {
        $this->attributes['referral_code'] = $value ?? $this->generateReferralCode();
    }

    private function generateReferralCode() {
        $hex = md5($this->attributes['uuid'] . uniqid("", true));
        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);
        $uid = preg_replace("#(*UTF8)[^A-Z0-9]#", "", $tmp);
        $len = max(4, min(128, 6));
        while (strlen($uid) < $len)
            $uid .= gen_uuid(22);

        return substr($uid, 0, $len);
    }
}