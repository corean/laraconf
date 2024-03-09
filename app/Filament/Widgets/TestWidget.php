<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendeeResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class TestWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.test-widget';

    #[On('undo')]
    public function undo(): void
    {
        ray('hi');
        return;
    }

    public function CallNotification(): Action
    {
        return Action::make('CallNotification')
            ->button()
            ->color('warning')
            ->label('Send a notification')
            ->action(function () {
                Notification::make('Test Notification')
                    ->success()
                    ->title('Test Notification')
                    ->body('This is a test notification')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('goToAttendees')
                            ->button()
                            ->color('primary')
                            ->url(AttendeeResource::getUrl('edit', ['record' => 1])),
                        \Filament\Notifications\Actions\Action::make('undo')
                            ->link()
                            ->color('gray')
                            ->dispatch('undo')
                    ])
                    ->send();
            });
    }
}
