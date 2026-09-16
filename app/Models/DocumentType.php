<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'custom_font_regular_path',
        'custom_font_bold_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    // Raccourci pratique : dernière version publiée
    public function publishedVersion()
    {
        return $this->hasOne(DocumentVersion::class)
            ->where('status', 'published')
            ->latestOfMany('published_at');
    }
}