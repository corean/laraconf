<?php

namespace App\Filament\Resources\TalkResource\Pages;

use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTalks extends ListRecords
{
    protected static string $resource = TalkResource::class;

    public function getTabs(): array
    {
        return [
            'All Talks'      => Tab::make(),
            'Approved' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', TalkStatus::APPROVED);
                }),
            'Submitted' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', TalkStatus::SUBMITTED);
                }),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', TalkStatus::REJECTED);
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
