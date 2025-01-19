<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Filament\Resources\TypeResource\RelationManagers;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }

    public static function getLabel(): ?string
    {
        return __('Type');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Types');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type_for')
                    ->label(__('Type'))
                    ->options([
                        "unit" => __("Unit"),
                        "hotel" => __("Hotel"),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_for')
                    ->label(__('Type'))
                    ->formatStateUsing(function ($state) {
                        return $state == "unit" ? __("Unit") : __("Hotel");
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_for')
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
