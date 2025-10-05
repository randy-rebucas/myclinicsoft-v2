<?php

namespace App\Services;

use App\Mail\AppointmentReminderMail;
use App\Mail\AppointmentCancellationMail;
use App\Mail\QueueUpdateMail;
use App\Mail\PrescriptionReadyMail;
use App\Mail\PasswordResetMail;
use App\Mail\UserCredentialsMail;
use App\Mail\AppUpdateMail;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send appointment reminder email.
     *
     * @param Appointment $appointment
     * @param User $user
     * @return bool
     */
    public function sendAppointmentReminder(Appointment $appointment, User $user): bool
    {
        try {
            Mail::to($user->email)->send(new AppointmentReminderMail($appointment, $user));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder email', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send appointment cancellation email.
     *
     * @param Appointment $appointment
     * @param User $user
     * @param string $reason
     * @return bool
     */
    public function sendAppointmentCancellation(Appointment $appointment, User $user, string $reason): bool
    {
        try {
            Mail::to($user->email)->send(new AppointmentCancellationMail($appointment, $user, $reason));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send appointment cancellation email', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send queue update email.
     *
     * @param Queue $queue
     * @param User $user
     * @param string $status
     * @return bool
     */
    public function sendQueueUpdate(Queue $queue, User $user, string $status): bool
    {
        try {
            Mail::to($user->email)->send(new QueueUpdateMail($queue, $user, $status));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send queue update email', [
                'queue_id' => $queue->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send prescription ready email.
     *
     * @param Prescription $prescription
     * @param User $user
     * @return bool
     */
    public function sendPrescriptionReady(Prescription $prescription, User $user): bool
    {
        try {
            Mail::to($user->email)->send(new PrescriptionReadyMail($prescription, $user));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send prescription ready email', [
                'prescription_id' => $prescription->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send password reset email.
     *
     * @param User $user
     * @param string $resetToken
     * @param string $resetUrl
     * @return bool
     */
    public function sendPasswordReset(User $user, string $resetToken, string $resetUrl): bool
    {
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetToken, $resetUrl));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send user credentials email.
     *
     * @param User $user
     * @param array $credentials
     * @return bool
     */
    public function sendUserCredentials(User $user, array $credentials): bool
    {
        try {
            Mail::to($user->email)->send(new UserCredentialsMail($credentials));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send user credentials email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send app update notification email.
     *
     * @param User $user
     * @param array $updateData
     * @return bool
     */
    public function sendAppUpdate(User $user, array $updateData): bool
    {
        try {
            Mail::to($user->email)->send(new AppUpdateMail($updateData));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send app update email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send bulk emails to multiple users.
     *
     * @param array $users
     * @param string $mailClass
     * @param array $data
     * @return array
     */
    public function sendBulkEmails(array $users, string $mailClass, array $data): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new $mailClass(...$data));
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Test email configuration.
     *
     * @param string $testEmail
     * @return bool
     */
    public function testEmailConfiguration(string $testEmail): bool
    {
        try {
            Mail::raw('This is a test email to verify email configuration.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('Email Configuration Test - ' . config('app.name'));
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Email configuration test failed', [
                'test_email' => $testEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
