<?php

namespace App\Models;

use App\Support\CacheVersion;
use App\Services\PhpMailerService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the role that owns the admin.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the login logs for the admin.
     */
    public function loginLogs(): MorphMany
    {
        return $this->morphMany(LoginLog::class, 'user', 'user_type', 'user_id');
    }

    /**
     * Get the refresh tokens for the admin.
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(AdminRefreshToken::class);
    }

    /**
     * Send the password reset notification using PHPMailer.
     */
    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = URL::route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], true);

        $expires = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
        $subject = 'Reset your password';
        $recipientName = $this->name ?: $this->email;

        $htmlBody = view('emails.password-reset', [
            'name' => $recipientName,
            'resetUrl' => $resetUrl,
            'expires' => $expires,
        ])->render();

        app(PhpMailerService::class)->send(
            $this->email,
            $recipientName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Check if admin is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        if ($this->role_id === null) {
            return false;
        }

        $superAdminRoleId = Cache::remember(CacheVersion::key('roles', 'super_admin:id'), 300, function () {
            return Role::query()->where('name', 'super_admin')->value('id');
        });

        if ($superAdminRoleId === null) {
            return false;
        }

        return (int) $this->role_id === (int) $superAdminRoleId;
    }

    /**
     * Check if admin has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true; // Super admin has all permissions
        }

        return $this->role && $this->role->hasPermission($permissionName);
    }

    /**
     * Check if admin has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if admin has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the identifier stored in the JWT subject claim.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
