<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_REPLIED = 'replied';

    public const CATEGORIES = [
        'general' => 'General Inquiry',
        'account' => 'Account & Login Issue',
        'billing' => 'Billing & Payment',
        'courses' => 'Courses & Batches',
        'technical' => 'Technical Support',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'category',
        'message',
        'status',
        'admin_reply',
        'replied_at',
        'replied_by',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * User who submitted the support ticket, if authenticated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Administrator who handled the reply.
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Scope query to pending messages.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope query to replied messages.
     */
    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    /**
     * Get human-friendly category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Check if message is currently pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if message has been replied to.
     */
    public function isReplied(): bool
    {
        return $this->status === self::STATUS_REPLIED;
    }

    /**
     * Build direct Gmail web compose URL with pre-filled recipient, subject, and body.
     */
    public function buildGmailComposeUrl(?string $replyText = null): string
    {
        $subject = "Re: {$this->subject} · LearnBuddy Support";

        $bodyContent = trim($replyText ?? '');
        $bodyContent .= "\n\n";
        $bodyContent .= "--------------------------------------------------\n";
        $bodyContent .= "Original Message from: {$this->name} ({$this->email})\n";
        if ($this->phone) {
            $bodyContent .= "Contact Number: {$this->phone}\n";
        }
        $bodyContent .= "Category: {$this->category_label}\n";
        $bodyContent .= "Date: " . $this->created_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A (T)') . "\n\n";
        $bodyContent .= "Message:\n" . $this->message;

        $params = http_build_query([
            'view' => 'cm',
            'fs' => '1',
            'to' => $this->email,
            'su' => $subject,
            'body' => $bodyContent,
        ]);

        return "https://mail.google.com/mail/?{$params}";
    }
}
