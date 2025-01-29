<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Forms\Components\MapInput;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
class CityResource extends Resource
{
    protected static ?string $model = City::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }

    public static function getLabel(): ?string
    {
        return __('City');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Cities');  // For plural label translations
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
                        ]),
                ])
                ->columnSpanFull(), // Makes sure the tabs take the full width

            FileUpload::make('images')
                ->label(__('Images'))
                ->disk('public')
                ->directory('cities')
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Creation Date'))
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
