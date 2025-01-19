<?php

namespace App\Filament\Resources\OwnerResource\RelationManagers;

use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Units');
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('owner_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('owner_id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__("ID"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__("Type")),
                Tables\Columns\TextColumn::make('name')
                    ->label(__("Name"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('unitType.name')
                    ->label(__("Unit Type"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__("City"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('compound.name')
                    ->label(__("Compound"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel.name')
                ->label(__('Hotel'))
                ->numeric()
                ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(__("Status"))
                    ->icon(fn (string $state): string => match ($state) {
                        'waiting' => 'heroicon-o-clock',
                        'active' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger'
                    }),                
                Tables\Columns\TextColumn::make('rate')
                    ->label(__('Rate'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'hotel' => __('Hotel Rooms'),
                        'unit' => __('Units'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'waiting' => __('Waiting'),
                        'active' => __('Active'),
                        'rejected' => __('Rejected'),
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                ->label(__('View'))
                ->url(fn(Unit $record) => ( '/admin/units/' . $record->id))
                ->openUrlInNewTab(false), // Ensure it doesn't open in a new tab
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
