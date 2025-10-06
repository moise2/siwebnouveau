<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $fillable = [
        'id',
        'type',
        'data',
        'read_at',
        'notifiable_id',
        'notifiable_type',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the route key for the notification.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Retourner l'utilisateur auquel la notification appartient.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Retourner une notification lue.
     */
    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    /**
     * Retourner une notification non lue.
     */
    public function markAsUnread()
    {
        $this->read_at = null;
        $this->save();
    }
}
