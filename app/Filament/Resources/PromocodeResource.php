<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromocodeResource\Pages;
use App\Filament\Resources\PromocodeResource\RelationManagers;
use App\Models\Promocode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;


class PromocodeResource extends Resource
{
    protected static ?string $model = Promocode::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Financial');
    }

    public static function getLabel(): ?string
    {
        return __('Promocode');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Promocodes');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')
                    ->tabs([
                        Tab::make(__('English'))
                            ->schema([
                                Forms\Components\Textarea::make('description.en')
                                    ->label(__('Description (English)'))
                                    ->columnSpanFull(),
                            ]),
                        Tab::make(__('Arabic'))
                            ->schema([
                                Forms\Components\Textarea::make('description.ar')
                                    ->label(__('Description (Arabic)'))
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('promocode')
                    ->label(__('Promocode'))
                    ->unique()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('percentage')
                    ->label(__('Percentage'))
                    ->suffix('%')
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get): bool => 
                        filled($get('amount_total')) || filled($get('amount_night'))
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('amount_total', null);
                            $set('amount_night', null);
                        }
                    }),
                Forms\Components\TextInput::make('amount_total')
                    ->label(__('Amount Total'))
                    ->suffix(__('EGP'))
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get): bool => 
                        filled($get('percentage')) || filled($get('amount_night'))
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('percentage', null);
                            $set('amount_night', null);
                        }
                    }),
                Forms\Components\TextInput::make('amount_night')
                    ->label(__('Amount Per Night'))
                    ->suffix(__('EGP'))
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get): bool => 
                        filled($get('percentage')) || filled($get('amount_total'))
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (filled($state)) {
                            $set('percentage', null);
                            $set('amount_total', null);
                        }
                    }),
                Forms\Components\DateTimePicker::make('expired_at')
                    ->label(__('Expired At'))
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->label(__('Active'))
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('promocode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_night')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPromocodes::route('/'),
            'create' => Pages\CreatePromocode::route('/create'),
            'edit' => Pages\EditPromocode::route('/{record}/edit'),
        ];
    }
}
