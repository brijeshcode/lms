<?php

namespace App\Models;

use App\Models\StudentDetail;
use App\Traits\Authorable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Authorable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'note',
        'active',
        'is_student',
        'mobile',
        'user_id', // who added this user
        'user_ip' // location
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
        'is_student' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'permissions',
        'role',
        'role_name'
    ];


    public function getPermissionsAttribute()
    {
        $role = $this->roles;
        if (count($role) > 0) {
            return $role[0]->permissions->pluck('name');
        }
        return [];
    }

    public function getRoleAttribute()
    {
        $role = $this->roles;
        if (count($role) > 0) {
            return collect($role[0])->except('permissions', 'created_at', 'updated_at', 'pivot');
            // return $role[0]->name;
        }
        return [];
    }

    public function getRoleNameAttribute()
    {
        $role = $this->roles;
        if (count($role) > 0) {
            return $role[0]->name;
        }
        return '';
    }

    public function details()
    {
        return $this->hasMany(StudentDetail::class, 'student_id');
    }
}
