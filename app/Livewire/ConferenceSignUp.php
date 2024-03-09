<?php

namespace App\Livewire;

use App\Models\Attendee;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ConferenceSignUp extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use interactsWithActions;

    public int $conferenceId;
    public int $price = 500;

    public function mount(): void
    {
        $this->conferenceId = 1;
    }

    public function signUpAction(): Action
    {
        return Action::make('signUp')
            ->slideOver()
            ->form([
                Forms\Components\Placeholder::make('total_price')
                    ->label('Total Price')
                    ->hiddenLabel()
                    ->content(function (Forms\Get $get) {
                        ray($get('attendees'))->label('attendees');
                        return '$'.number_format($this->price * count($get('attendees')));
                    }),
                Forms\Components\Repeater::make('attendees')
                    ->schema(Attendee::getForm()),
            ])
            ->action(function (array $data) {
                ray($this->conferenceId, $data)->label('signUpAction');

                collect($data['attendees'])
                    ->each(fn ($data) => Attendee::create([
                        'name'          => $data['name'],
                        'email'         => $data['email'],
                        'ticket_cost'   => $this->price,
                        'is_paid'       => true,
                        'conference_id' => $this->conferenceId,
                    ]));
            })
            ->after(function () {
                Notification::make()
                    ->success()
                    ->title('Attendees signed up successfully!')
                    ->body(new HtmlString('You have successfully signed up for the conference.'))
                    ->send();
            });
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.conference-sign-up');
    }
}
