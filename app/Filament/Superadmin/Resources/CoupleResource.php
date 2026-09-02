<?php

namespace App\Filament\Superadmin\Resources;

use App\Filament\Superadmin\Resources\CoupleResource\Pages;
use App\Models\Couple;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoupleResource extends Resource
{
    protected static ?string $model = Couple::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 9;

    protected static ?string $modelLabel = 'Pasangan';

    protected static ?string $pluralModelLabel = 'Pasangan';

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'superadmin';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pasangan')
                    ->placeholder('Contoh: Ahmad & Fatimah')
                    ->required()
                    ->maxLength(255),
                TextInput::make('kode')
                    ->label('Kode')
                    ->placeholder('Contoh: PAS001')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->badge(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Members')
                    ->counts('users')
                    ->badge()
                    ->color('primary'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouples::route('/'),
            'create' => Pages\CreateCouple::route('/create'),
            'edit' => Pages\EditCouple::route('/{record}/edit'),
        ];
    }
}
