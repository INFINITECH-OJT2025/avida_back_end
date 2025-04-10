<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyMedia extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'url', 'type'];

    // ✅ Relationship: Media belongs to a Property
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    // ✅ Accessor to automatically format the storage URL// PropertyMedia.php
    public function getUrlAttribute($value)
    {
        return $value; // ✅ Return the stored path only: 'property_lightbox_media/1/filename.jpg'
    }
    
    
}
