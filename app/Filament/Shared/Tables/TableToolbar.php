<?php

namespace App\Filament\Shared\Tables;

use Filament\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class TableToolbar
{
    public static function configure(Table $table, string $recordLabel, ?string $additionalClass = null): Table
    {
        return $table
            ->extraAttributes([
                'class' => collect(['bank-data-table', $additionalClass])
                    ->filter()
                    ->implode(' '),
            ])
            ->filtersTriggerAction(fn (Action $action): Action => $action
                ->button()
                ->label('Filter')
                ->extraAttributes(fn (HasTable $livewire): array => [
                    'class' => 'bank-table-filter-trigger',
                    'data-active-filters-count' => (string) $livewire->getTable()->getActiveFiltersCount(),
                ]))
            ->toolbarActions([
                Action::make('recordCount')
                    ->view('filament.shared.table-record-count')
                    ->viewData(fn (HasTable $livewire): array => [
                        'count' => $livewire->getFilteredTableQuery()?->count() ?? 0,
                        'label' => $recordLabel,
                    ]),
            ]);
    }
}
