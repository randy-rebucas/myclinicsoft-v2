<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send a notification to a user.
     *
     * @param User $user
     * @param string $type
     * @param array $data
     * @return Notification
     */
    public function sendToUser(User $user, string $type, array $data = []): Notification
    {
        return Notification::create([
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => $data,
        ]);
    }

    /**
     * Send a notification to multiple users.
     *
     * @param Collection $users
     * @param string $type
     * @param array $data
     * @return Collection
     */
    public function sendToUsers(Collection $users, string $type, array $data = []): Collection
    {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push($this->sendToUser($user, $type, $data));
        }

        return $notifications;
    }

    /**
     * Send appointment reminder notification.
     *
     * @param User $user
     * @param array $appointmentData
     * @return Notification
     */
    public function sendAppointmentReminder(User $user, array $appointmentData): Notification
    {
        return $this->sendToUser($user, 'appointment_reminder', [
            'title' => 'Appointment Reminder',
            'message' => "You have an appointment scheduled for {$appointmentData['date']} at {$appointmentData['time']}",
            'appointment_id' => $appointmentData['id'],
            'doctor_name' => $appointmentData['doctor_name'],
            'clinic_name' => $appointmentData['clinic_name'],
        ]);
    }

    /**
     * Send queue update notification.
     *
     * @param User $user
     * @param array $queueData
     * @return Notification
     */
    public function sendQueueUpdate(User $user, array $queueData): Notification
    {
        return $this->sendToUser($user, 'queue_update', [
            'title' => 'Queue Update',
            'message' => "Your queue number {$queueData['queue_number']} is now {$queueData['status']}",
            'queue_id' => $queueData['id'],
            'clinic_name' => $queueData['clinic_name'],
        ]);
    }

    /**
     * Send prescription ready notification.
     *
     * @param User $user
     * @param array $prescriptionData
     * @return Notification
     */
    public function sendPrescriptionReady(User $user, array $prescriptionData): Notification
    {
        return $this->sendToUser($user, 'prescription_ready', [
            'title' => 'Prescription Ready',
            'message' => "Your prescription is ready for pickup",
            'prescription_id' => $prescriptionData['id'],
            'medication_name' => $prescriptionData['medication_name'],
            'clinic_name' => $prescriptionData['clinic_name'],
        ]);
    }

    /**
     * Mark notification as read.
     *
     * @param Notification $notification
     * @return Notification
     */
    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param User $user
     * @return int
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notifications for a user.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getUnreadNotifications(User $user, int $limit = 10): Collection
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all notifications for a user.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getAllNotifications(User $user, int $limit = 20): Collection
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old notifications.
     *
     * @param int $daysOld
     * @return int
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))->delete();
    }
}
