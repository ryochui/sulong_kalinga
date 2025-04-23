<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'conversation_id';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'is_group_chat',
        'last_message_id'
    ];
    
    /**
     * Get the participants of this conversation.
     */
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class, 'conversation_id');
    }
    
    /**
     * Get all messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')->orderBy('message_timestamp', 'asc');
    }
    
    /**
     * Get the last message of this conversation.
     */
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
    
    /**
     * Check if a specific user is a participant in this conversation.
     *
     * @param int $participantId
     * @param string $participantType
     * @return bool
     */
    public function hasParticipant($participantId, $participantType)
    {
        return $this->participants()
            ->where('participant_id', $participantId)
            ->where('participant_type', $participantType)
            ->whereNull('left_at')
            ->exists();
    }
    
    /**
     * Get all staff participants (users)
     */
    public function staffParticipants()
    {
        return $this->participants()
            ->where('participant_type', 'cose_staff')
            ->whereNull('left_at');
    }
    
    /**
     * Get all beneficiary participants
     */
    public function beneficiaryParticipants()
    {
        return $this->participants()
            ->where('participant_type', 'beneficiary')
            ->whereNull('left_at');
    }
    
    /**
     * Get all family member participants
     */
    public function familyMemberParticipants()
    {
        return $this->participants()
            ->where('participant_type', 'family_member')
            ->whereNull('left_at');
    }
}