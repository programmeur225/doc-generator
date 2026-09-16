<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_version_id',
        'user_id',
        'data',
        'pdf_path',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}