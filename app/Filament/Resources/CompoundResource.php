<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompoundResource\Pages;
use App\Filament\Resources\CompoundResource\RelationManagers;
use App\Models\Compound;
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
class CompoundResource extends Resource
{
    protected static ?string $model = Compound::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Add Data');
    }

    public static function getLabel(): ?string
    {
        return __('Compound');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Compounds');  // For plural label translations
    }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Tabs::make('Translations')
                ->tabs([
                    Tab::make('English')
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
                    Tab::make('Arabic')
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
                ->columnSpanFull(),
            Forms\Components\Select::make('city_id')
                ->label(__('City'))
                ->options(\App\Models\City::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->placeholder(__('Select a city'))
                ->reactive(),
                Forms\Components\FileUpload::make('images')
                ->label(__('Images'))
                ->disk('public')
                ->directory('compounds')
                ->multiple()
                ->columnSpanFull()
                ->reorderable()
                ->panelLayout('grid')
                ->image()
                ->required(),
            Forms\Components\TextInput::make('address')
                ->label(__('Address'))
                ->required()
                ->maxLength(255),
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
                    ->label(__('City')),
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
            'index' => Pages\ListCompounds::route('/'),
            'create' => Pages\CreateCompound::route('/create'),
            'edit' => Pages\EditCompound::route('/{record}/edit'),
        ];
    }
}
