<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'attempted_at',
        'locked_until',
        'attempts_count',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    public static function recordAttempt(string $email, string $ip, ?string $userAgent, bool $successful): self
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'attempted_at' => now(),
        ]);
    }

    public static function getFailedAttemptsCount(string $email, int $minutesBack = 15): int
    {
        return self::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutesBack))
            ->count();
    }

    public static function isAccountLocked(string $email): bool
    {
        $latestAttempt = self::where('email', $email)
            ->latest('attempted_at')
            ->first();

        if (!$latestAttempt || !$latestAttempt->locked_until) {
            return false;
        }

        return $latestAttempt->locked_until->isFuture();
    }

    public static function lockAccount(string $email, int $minutes = 30): void
    {
        $latestAttempt = self::where('email', $email)
            ->latest('attempted_at')
            ->first();

        if ($latestAttempt) {
            $latestAttempt->update([
                'locked_until' => now()->addMinutes($minutes)
            ]);
        }
    }
}
