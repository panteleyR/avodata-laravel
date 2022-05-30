<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_super_admin', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function isAdmin()
    {
        return !$this->isSuperAdmin() && $this->currentRole() === 'Admin';
    }

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }

    public function isAnalytic()
    {
        return !$this->isSuperAdmin() && $this->currentRole() === 'Analytic';
    }

    public function isEmployee()
    {
        return !$this->isSuperAdmin() && $this->currentRole() === 'Employee';
    }

    public function cabinets()
    {
        return $this->members()->with('cabinet')->get()->map(function ($member) {
            return $member->cabinet;
        });
    }

    public function members()
    {
        return $this->hasMany('App\Models\Member', 'user_id');
    }

    public function currentMember()
    {
        $request = request();
        $cabinetId = $request->cabinetId;
        return $this->members()->where('cabinet_id', $cabinetId)->with('role', 'cabinet')->first();
    }

    public function currentRole()
    {
        $member = $this->currentMember();
        return $member->role->name;
    }

    public function currentCabinet()
    {
        $member = $this->currentMember();
        return $member->cabinet;
    }
}
