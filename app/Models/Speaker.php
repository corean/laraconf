<?php

namespace App\Models;

use Filament\Forms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    protected const QUALIFICATIONS = [
        'business-leader'       => 'Business Leader',
        'charisma'              => 'Charismatic Speaker',
        'first-time'            => 'First Time Speaker',
        'hometown-hero'         => 'Hometown Hero',
        'humanitarian'          => 'Works in Humanitarian Field',
        'laracasts-contributor' => 'Laracasts Contributor',
        'twitter-influencer'    => 'Large Twitter Following',
        'youtube-influencer'    => 'Large YouTube Following',
        'open-source'           => 'Open Source Creator / Maintainer',
        'unique-perspective'    => 'Unique Perspective',
    ];

    protected $casts = [
        'id'             => 'integer',
        'qualifications' => 'array',
    ];

    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\FileUpload::make('avatar')
                ->avatar()
                ->maxSize(1024 * 1024 * 2)
            // ->preview(fn (Speaker $speaker) => $speaker->avatar)
            // ->delete(function (Speaker $speaker) {
            //     $speaker->update(['avatar' => null]);
            // })
            ,
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('bio')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('twitter_handle'),
            Forms\Components\CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->columns(3)
                ->searchable()
                ->options(static::QUALIFICATIONS)
                ->descriptions([
                    'business-leader' => 'Here is a nice long description',
                    'charisma'        => 'This is even more information about why you should pick this one',
                ]),
        ];
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }
}
