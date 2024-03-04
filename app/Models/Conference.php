<?php

namespace App\Models;

use App\Enums\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class,
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Conference Name')
                ->rules(['required', 'string', 'max:60'])
                ->markAsRequired(),
            Forms\Components\MarkdownEditor::make('description')
                ->required(),
            Forms\Components\DateTimePicker::make('start_date')
                ->required(),
            Forms\Components\DateTimePicker::make('end_date')
                ->required(),
            Forms\Components\Checkbox::make('is_published')
                ->default(true),
            Forms\Components\Select::make('status')
                ->options([
                    'draft'     => 'Draft',
                    'published' => 'Published',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),
            Forms\Components\Select::make('region')
                ->enum(Region::class)
                ->options(Region::class)
                ->live()
                ->required(),
            Forms\Components\Select::make('venue_id')
                ->searchable()
                ->preload()
                ->editOptionForm( Venue::getForm())
                ->createOptionForm( Venue::getForm())
                ->relationship(
                    name: 'venue',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                        ray($query->toSql(), $get('region'));
                        return $query->where('region', $get('region'));
                    }),
            Forms\Components\CheckboxList::make('speakers')
                ->columnSpanFull()
                ->columns(3)
                ->options(Speaker::all()->pluck('name', 'id')->toArray())
                ->required(),
        ];
    }
}
