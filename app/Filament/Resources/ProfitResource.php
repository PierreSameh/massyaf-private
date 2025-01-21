<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfitResource\Pages;
use App\Filament\Resources\ProfitResource\RelationManagers;
use App\Models\Profit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfitResource extends Resource
{
    protected static ?string $model = Profit::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Financial');
    }

    public static function getLabel(): ?string
    {
        return __('Profit');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Profits');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__("Type"))
                    ->options([
                        "unit" => __("Unit"),
                        "hotel" => __("Hotel"),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('from')
                    ->label(__("Price From"))
                    ->required()
                    ->suffix(__('EGP'))
                    ->numeric(),
                Forms\Components\TextInput::make('to')
                    ->label(__("Price To"))
                    ->suffix(__('EGP'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('percentage')
                    ->label(__('Percentage of booking value'))
                    ->required()
                    ->suffix('%')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->label(__("Type")),
                Tables\Columns\TextColumn::make('from')
                    ->label(__("Price From"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to')
                    ->label(__("Price To"))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label(__('Percentage of booking value'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__("Type"))
                    ->options([
                        "unit" => __("Unit"),
                        "hotel" => __("Hotel"),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProfits::route('/'),
            'create' => Pages\CreateProfit::route('/create'),
            'edit' => Pages\EditProfit::route('/{record}/edit'),
        ];
    }
}
