<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;
    
    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'conversation_participant_id';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'conversation_id',
        'participant_id',
        'participant_type',
        'joined_at',
        'left_at'
    ];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];
    
    /**
     * Get the conversation that this participant belongs to.
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
    
    /**
     * Get the participant entity (User, Beneficiary, or FamilyMember).
     */
    public function participant()
    {
        if ($this->participant_type === 'cose_staff') {
            return $this->belongsTo(User::class, 'participant_id');
        } elseif ($this->participant_type === 'beneficiary') {
            return $this->belongsTo(Beneficiary::class, 'participant_id', 'beneficiary_id');
        } elseif ($this->participant_type === 'family_member') {
            return $this->belongsTo(FamilyMember::class, 'participant_id', 'family_member_id');
        }
        
        return null;
    }
    
    /**
     * Check if this participant is active (has not left the conversation)
     */
    public function isActive()
    {
        return $this->left_at === null;
    }
}