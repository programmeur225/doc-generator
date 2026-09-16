<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_version_id',
        'page_number',
        'image_path',
        'image_width',
        'image_height',
    ];

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function variables()
    {
        return $this->hasMany(Variable::class);
    }
}