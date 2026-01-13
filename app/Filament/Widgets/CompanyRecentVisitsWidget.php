<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Services\VisitScopeService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyRecentVisitsWidget extends TableWidget
{
    public ?Model $record = null;

    protected static ?string $heading = 'Recent Visits to Company';

    public function table(Table $table): Table
    {
        if (!$this->record instanceof Company) {
            return $table->query(fn() => \App\Models\Visit::query()->whereRaw('1=0'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);

        return $table
            ->query(
                fn (): Builder => $service->getVisitQuery($user)
                    ->where('company_id', $this->record->id)
                    ->latest('visit_started_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Visitor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_started_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(50),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5);
    }
}