<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type_id',
        'version_number',
        'status',
        'notes',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function pages()
    {
        return $this->hasMany(DocumentPage::class)->orderBy('page_number');
    }

    public function variables()
    {
        return $this->hasMany(Variable::class)->orderBy('display_order');
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helpers d'état
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    // Clone complet (pages + variables) pour créer une nouvelle version
    public function duplicate(string $newVersionNumber): self
    {
        $newVersion = $this->replicate();
        $newVersion->version_number = $newVersionNumber;
        $newVersion->status = 'draft';
        $newVersion->published_at = null;
        $newVersion->save();

        $pageMap = []; // ancien id => nouveau modèle

        foreach ($this->pages as $page) {
            $newPage = $page->replicate();
            $newPage->document_version_id = $newVersion->id;
            $newPage->save();
            $pageMap[$page->id] = $newPage;
        }

        foreach ($this->variables as $variable) {
            $newVariable = $variable->replicate();
            $newVariable->document_version_id = $newVersion->id;
            $newVariable->document_page_id = $pageMap[$variable->document_page_id]->id;
            $newVariable->save();
        }

        return $newVersion;
    }
}