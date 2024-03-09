<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id'         => 'integer',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'venue_id'   => 'integer',
        'region'     => Region::class,
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

    public function attendees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\Section::make('Conference Information')
                ->collapsible()
                ->description('Provide some basic information about the conference.')
                ->icon('heroicon-o-information-circle')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Conference Name')
                        ->rules(['required', 'string', 'max:60'])
                        ->markAsRequired(),
                    Forms\Components\MarkdownEditor::make('description')
                        ->required(),
                    Forms\Components\DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),
                    Forms\Components\Fieldset::make('Status')
                        ->columns(1)
                        ->schema([
                            Forms\Components\Toggle::make('is_published')
                                ->default(true),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft'     => 'Draft',
                                    'published' => 'Published',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ]),
                ]),
            Forms\Components\Section::make('Location')
                ->columns(2)
                ->schema([

                    Forms\Components\Select::make('region')
                        ->enum(Region::class)
                        ->options(Region::class)
                        ->live()
                        ->required(),
                    Forms\Components\Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->editOptionForm(Venue::getForm())
                        ->createOptionForm(Venue::getForm())
                        ->relationship(
                            name: 'venue',
                            titleAttribute: 'name',
                            modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                ray($query->toSql(), $get('region'));
                                return $query->where('region', $get('region'));
                            }),
                    Actions::make([
                        Actions\Action::make('star')
                            ->label('Fill with Factory Data')
                            ->icon('heroicon-m-star')
                            ->visible(function (string $operation) {
                                return $operation === 'create' && app()->environment('local');
                            })
                            ->action(function ($livewire) {
                                $conference = Conference::factory()->make()->toArray();
                                unset($conference['venue_id']);
                                $livewire->form->fill($conference);
                            }),
                    ]),
                ]),

            Forms\Components\CheckboxList::make('speakers')
                ->columnSpanFull()
                ->columns(3)
                ->options(Speaker::all()->pluck('name', 'id')->toArray())
                ->required(),
        ];
    }
}
