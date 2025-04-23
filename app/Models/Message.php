<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'message_id';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
        'message_timestamp'
    ];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'message_timestamp' => 'datetime',
    ];
    
    /**
     * Get the conversation that this message belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
    
    /**
     * Get the sender entity (User, Beneficiary, or FamilyMember).
     */
    public function sender()
    {
        if ($this->sender_type === 'cose_staff') {
            return $this->belongsTo(User::class, 'sender_id');
        } elseif ($this->sender_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'sender_id', 'beneficiary_id');
        } elseif ($this->sender_type === 'family_member') {
            return $this->belongsTo(FamilyMember::class, 'sender_id', 'family_member_id');
        }
        
        return null;
    }
    
    /**
     * Get all attachments for this message.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }
    
    /**
     * Get all read statuses for this message.
     */
    public function readStatuses()
    {
        return $this->hasMany(MessageReadStatus::class, 'message_id');
    }
    
    /**
     * Check if this message has been read by a specific user.
     *
     * @param int $readerId
     * @param string $readerType
     * @return bool
     */
    public function isReadBy($readerId, $readerType)
    {
        return $this->readStatuses()
            ->where('reader_id', $readerId)
            ->where('reader_type', $readerType)
            ->exists();
    }
    
    /**
     * Mark this message as read by a specific user.
     *
     * @param int $readerId
     * @param string $readerType
     * @return \App\Models\MessageReadStatus
     */
    public function markAsReadBy($readerId, $readerType)
    {
        // Don't create duplicate read statuses
        $existingStatus = $this->readStatuses()
            ->where('reader_id', $readerId)
            ->where('reader_type', $readerType)
            ->first();
            
        if ($existingStatus) {
            return $existingStatus;
        }
        
        return MessageReadStatus::create([
            'message_id' => $this->message_id,
            'reader_id' => $readerId,
            'reader_type' => $readerType,
            'read_at' => now()
        ]);
    }
    
    /**
     * Check if this message has attachments.
     */
    public function hasAttachments()
    {
        return $this->attachments()->exists();
    }
}