<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Forms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talk extends Model
{
    use HasFactory;

    protected $casts = [
        'id'         => 'integer',
        'speaker_id' => 'integer',
        'status'     => TalkStatus::class,
        'length'     => TalkLength::class,
    ];

    public static function getForm($spekerId = null): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->required(),
            Forms\Components\RichEditor::make('abstract')
                ->required()
                ->columnSpanFull(),
            Forms\Components\Select::make('speaker_id')
                ->hidden($spekerId !== null)
                ->relationship('speaker', 'name')
                ->required(),
        ];
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }
}
