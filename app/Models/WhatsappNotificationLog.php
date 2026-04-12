<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WhatsappNotificationLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_notification_logs';

    protected $fillable = [
        'to_phone', 'template_name', 'template_params', 'message_body',
        'twilio_sid', 'status', 'error_message',
        'notifiable_type', 'notifiable_id',
        'sent_at', 'delivered_at',
    ];

    protected $casts = [
        'template_params' => 'array',
        'sent_at'         => 'datetime',
        'delivered_at'    => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeFailed($query)  { return $query->where('status', 'failed'); }
}
