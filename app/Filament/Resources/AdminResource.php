<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getLabel(): ?string
    {
        return __('Admin');  // Translation function works here
    }
    public static function getPluralLabel(): ?string
    {
        return __('Admins');  // For plural label translations
    }
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_admin'); // Restrict access to users with the 'view_admin' permission
    }

    // Restrict access to the create page
    public static function canCreate(): bool
    {
        return auth()->user()->can('create_admin');
    }

    // Restrict access to the edit page
    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_admin');
    }

    // Restrict access to the delete action
    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__("Name"))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__("Email"))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->label(__("Phone Number"))
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label(__("Password"))
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->hidden()
                    ->default('admin'),   
                Forms\Components\Select::make('roles')
                    ->label(__('Roles'))
                    ->options(Role::all()->pluck('name', 'id')) // Fetch all roles
                    ->multiple() // Allow multiple roles to be selected
                    ->preload(), // Enable searching for roles
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->label(__("ID")),
                Tables\Columns\TextColumn::make('name')->searchable()->label(__("Name")),
                Tables\Columns\TextColumn::make('email')->searchable()
                    ->label(__("Email")),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->formatStateUsing(function ($state) {
                        // Ensure $state is an array before using implode
                        return is_array($state) ? implode(', ', $state) : $state;
    }) // Display roles as a comma-separated list
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'admin');
    }
}
