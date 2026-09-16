<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use HasFactory;

    protected $table = 'variables';

    protected $fillable = [
        'document_version_id',
        'document_page_id',
        'key',
        'label',
        'type',
        'is_required',
        'options',
        'placeholder',
        'display_order',
        'position_x',
        'position_y',
        'box_width',
        'box_height',
        'font_family',
        'font_size',
        'font_color',
        'background_color',
        'auto_font_size',
        'text_align',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'position_x' => 'float',
        'position_y' => 'float',
        'box_width' => 'float',
        'box_height' => 'float',
        'font_size' => 'integer',
        'auto_font_size' => 'boolean',
    ];

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function documentPage()
    {
        return $this->belongsTo(DocumentPage::class);
    }

    // Règle de validation Laravel générée dynamiquement selon le type
    public function validationRule(): string
    {
        $rules = [$this->is_required ? 'required' : 'nullable'];

        $rules[] = match ($this->type) {
            'number' => 'numeric',
            'date' => 'date',
            'select' => 'in:' . implode(',', $this->options ?? []),
            'checkbox' => 'boolean',
            default => 'string|max:255',
        };

        return implode('|', $rules);
    }
}