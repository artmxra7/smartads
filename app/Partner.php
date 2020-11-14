<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Partner extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'partners';

    protected $guard = 'partner';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
         'partner_code',
         'name',
         'email',
         'password',
         'partner_ktp',
         'partner_hp',
         'partner_npwp',
         'partner_company',
         'provinsi_code',
         'kota_code',
         'kec_code',
         'kel_code',
         'partner_address',
         'partner_verification_type',
         'partner_status'
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
        'email_verified_at' => 'datetime',
    ];

    const CREATED_AT = 'partner_date_created';
    const UPDATED_AT = 'partner_date_updated';
}
