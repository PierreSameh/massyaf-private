<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Forms\Components\MapInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }
    public static function getLabel(): ?string
    {
        return __('Hotel');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Hotels');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')
                ->tabs([
                    Tab::make(__('English'))
                        ->schema([
                            TextInput::make('name.en')
                                ->label(__('Name (English)'))
                                ->required()
                                ->maxLength(255),
                            RichEditor::make('description.en')
                                ->label(__('Description (English)'))
                                ->columnSpanFull(),
                            RichEditor::make('features.en')
                                ->label(__('Features (English)'))
                                ->columnSpanFull(),
                            RichEditor::make('Details.en')
                                ->label(__('Details (English)'))
                                ->columnSpanFull(),
                        ]),
                    Tab::make(__('Arabic'))
                        ->schema([
                            TextInput::make('name.ar')
                                ->label(__('Name (Arabic)'))
                                ->required()
                                ->maxLength(255),
                            RichEditor::make('description.ar')
                                ->label(__('Description (Arabic)'))
                                ->columnSpanFull(),
                            RichEditor::make('features.ar')
                                ->label(__('Features (Arabic)'))
                                ->columnSpanFull(),
                            RichEditor::make('details.ar')
                                ->label(__('Details (Arabic)'))
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
                Forms\Components\TextInput::make('address')
                    ->label(__('Address'))
                    ->maxLength(255),
                Forms\Components\Select::make('city_id')
                    ->label(__('City'))
                    ->options(\App\Models\City::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder(__('Select a city'))
                    ->reactive(),
                TextInput::make('base_code')
                    ->label(__('Base Code Generator'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('images')
                    ->label(__('Images'))
                    ->disk('public')
                    ->directory('hotels')
                    ->multiple()
                    ->columnSpanFull()
                    ->reorderable()
                    ->panelLayout('grid')
                    ->image()
                    ->required(),
                MapInput::make('coordinates')
                    ->label(__("Zone Boundaries"))
                    ->apiKey(env('GOOGLE_MAPS_API_KEY'))
                    ->reactive()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__("City"))
                    ->searchable(),
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
