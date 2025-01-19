<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatResource\Pages;
use App\Filament\Resources\ChatResource\RelationManagers;
use App\Models\Chat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class ChatResource extends Resource
{
    protected static ?string $model = Chat::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getLabel(): ?string
    {
        return __('Chat');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Chats');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->sortable()->searchable(),
                TextColumn::make('user.name')->label(__('User'))->searchable(),
                TextColumn::make('owner.name')->label(__('Owner'))->searchable(),
                TextColumn::make('messages_count')
                    ->label(__('Messages'))
                    ->counts('messages'),
                TextColumn::make('created_at')->label(__('Creation Date'))->dateTime(),
                TextColumn::make('deleted_at')->label(__('Deleted At'))->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // Add a filter for trashed records
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->hidden(fn ($record) => $record->trashed()), // Add a View action to open the custom page
                Tables\Actions\RestoreAction::make(),
            ])
            ->recordUrl(function ($record) {
                // Only allow clicking on non-deleted records
                return $record->trashed() ? null : static::getUrl('view', ['record' => $record]);
            });
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
            'index' => Pages\ListChats::route('/'),
            'create' => Pages\CreateChat::route('/create'),
            'view' => Pages\ViewChat::route('/{record}'),
            'edit' => Pages\EditChat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withTrashed(); // Include soft-deleted records
}
}
