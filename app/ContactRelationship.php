<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactRelationship extends Model
{
    protected $fillable = [
        'contact_id',
        'related_contact_id',
        'relationship_type',
        'business_id'
    ];

    /**
     * Get the primary contact
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the related contact
     */
    public function relatedContact()
    {
        return $this->belongsTo(Contact::class, 'related_contact_id');
    }

    /**
     * Get the business
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
