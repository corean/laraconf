<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Second Group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    })
                    ->searchable(),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function (Talk $record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name='.urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->label('Speaker')
                    ->sortable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('new_talk')
                    ->boolean(),
                // Tables\Columns\ToggleColumn::make('new_talk')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->color(fn (TalkStatus $state) => $state->getColor())
                    ->badge(),
                Tables\Columns\IconColumn::make('length')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL    => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE   => 'heroicon-o-key',
                        };
                    })
                    ->label('Length')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk')
                    ->label('New Talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->label('Speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('avatar')
                    ->label('show only speakers with avatars')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function ($query) {
                            $query->whereNotNull('avatar');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Talk $talk) => $talk->status === TalkStatus::SUBMITTED)
                        ->action(fn (Talk $talk) => $talk->update(['status' => TalkStatus::APPROVED]))
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Talk Approved')
                                ->duration(1000)
                                ->body('The talk has been approved.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (Talk $talk) => $talk->status === TalkStatus::SUBMITTED)
                        ->action(fn (Talk $talk) => $talk->update(['status' => TalkStatus::REJECTED]))
                        ->after(function () {
                            Notification::make()
                                ->danger()
                                ->title('Talk Approved')
                                ->duration(1000)
                                ->body('The talk has been approved.')
                                ->send();
                        }),
                ]),

            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export')
                    ->action(function (Forms\Components\Livewire $livewire) {
                        ray($livewire->getFilteredTableQuery());
                        ray('Exporting talks...');
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $talks) => $talks->each->update(['status' => TalkStatus::APPROVED])),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->action(fn (Collection $talks) => $talks->each->update(['status' => TalkStatus::REJECTED])),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit'   => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
