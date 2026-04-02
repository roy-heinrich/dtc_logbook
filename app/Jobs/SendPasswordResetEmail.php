<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Services\PhpMailerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendPasswordResetEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(
        public int $adminId,
        public string $token
    ) {}

    public function handle(PhpMailerService $phpMailerService): void
    {
        $admin = Admin::query()->find($this->adminId);

        if (! $admin) {
            return;
        }

        $resetUrl = URL::route('password.reset', [
            'token' => $this->token,
            'email' => $admin->email,
        ], true);

        $expires = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
        $subject = 'Reset your password';
        $recipientName = $admin->name ?: $admin->email;

        $htmlBody = view('emails.password-reset', [
            'name' => $recipientName,
            'resetUrl' => $resetUrl,
            'expires' => $expires,
        ])->render();

        $phpMailerService->send(
            $admin->email,
            $recipientName,
            $subject,
            $htmlBody
        );
    }
}
