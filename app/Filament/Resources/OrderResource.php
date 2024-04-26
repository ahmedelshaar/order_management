<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'طلبات';
    protected static ?string $pluralLabel = 'طلبات';
    protected static ?string $modelLabel = 'طلب';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('user');
        if (auth()->user()->role === 2) {
            return $query;
        }
        return $query->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الطلب')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('رقم الطلب')
                            ->hiddenOn(['create'])
                            ->disabledOn(['edit']),
                        Forms\Components\TextInput::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->hiddenOn(['create'])
                            ->disabledOn(['edit']),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->required()
                            ->options([
                                OrderStatusEnum::NEW => 'جديد',
                                OrderStatusEnum::ACCEPTED => 'مقبول',
                                OrderStatusEnum::REJECTED => 'مرفوض',
                                OrderStatusEnum::PROCESSING => 'قيد المعالجة',
                                OrderStatusEnum::HIGH_PRIORITY_PROCESSING => 'معالجة ذات أولوية عالية',
                                OrderStatusEnum::NO_RESPONSE => 'لا يوجد رد',
                                OrderStatusEnum::INQUIRY => 'استفسار',
                                OrderStatusEnum::CANCELED => 'ملغى',
                                OrderStatusEnum::COMPLETED => 'مكتمل',
                                OrderStatusEnum::DOCUMENTS_DELIVERED => 'تم تسليم الوثائق',
                            ])
                            ->default(1),
                        Forms\Components\Select::make('user_id')
                            ->label('المسؤول')
                            ->searchable()
                            ->preload()
                            ->relationship('user', 'name'),
                    ])
                    ->columns(2),
                Forms\Components\Split::make([
                    Forms\Components\Section::make('بيانات العميل')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('الاسم')
                                ->required(),
                            Forms\Components\TextInput::make('age')
                                ->required()
                                ->label('العمر')
                                ->numeric(),
                            Forms\Components\TextInput::make('mobile_number')
                                ->label('رقم الجوال')
                                ->required(),
                            Forms\Components\Select::make('nationality')
                                ->label('الجنسية')
                                ->options([
                                    'سعودي' => 'سعودي',
                                    'غير سعودي' => 'غير سعودي',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('city')
                                ->label('المدينة')
                                ->required()
                        ]),
                    Forms\Components\Section::make('بيانات المالية والوظيفة')
                        ->schema([
                            Forms\Components\TextInput::make('company_name')
                                ->label('اسم الشركة')
                                ->required(),
                            Forms\Components\Split::make([
                                Forms\Components\TextInput::make('salary')
                                    ->label('الراتب')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('bank')
                                    ->label('البنك')
                                    ->required(),
                            ])->columnSpanFull(),
                            Forms\Components\Select::make('liabilities')
                                ->label('الالتزامات')
                                ->options([
                                    1 => 'نعم',
                                    0 => 'لا',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('liabilities_description')
                                ->label('وصف الالتزامات')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('installment')
                                ->label('القسط')
                                ->required()
                                ->numeric(),
                        ])
                ])->columnSpanFull(),
                Forms\Components\Section::make('بيانات السيارة')
                    ->schema([
                        Forms\Components\TextInput::make('car_brand')
                            ->label('الماركة')
                            ->required(),
                        Forms\Components\TextInput::make('car_name')
                            ->label('النوع')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المسؤول')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        OrderStatusEnum::NEW => 'جديد',
                        OrderStatusEnum::ACCEPTED => 'مقبول',
                        OrderStatusEnum::REJECTED => 'مرفوض',
                        OrderStatusEnum::PROCESSING => 'قيد المعالجة',
                        OrderStatusEnum::HIGH_PRIORITY_PROCESSING => 'معالجة ذات أولوية عالية',
                        OrderStatusEnum::NO_RESPONSE => 'لا يوجد رد',
                        OrderStatusEnum::INQUIRY => 'استفسار',
                        OrderStatusEnum::CANCELED => 'ملغى',
                        OrderStatusEnum::COMPLETED => 'مكتمل',
                        OrderStatusEnum::DOCUMENTS_DELIVERED => 'تم تسليم الوثائق',
                    })
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('العمر')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('رقم الجوال')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('الجنسية')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('المدينة')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('اسم الشركة')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label('الراتب')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank')
                    ->label('البنك')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('liabilities')
                    ->label('الالتزامات')
                    ->toggleable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('installment')
                    ->label('القسط')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car_brand')
                    ->label('الماركة')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('car_name')
                    ->label('النوع')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ التعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('المسؤول')
                    ->preload()
                    ->searchable()
                    ->relationship('user', 'name', fn(Builder $query) => $query->where('role', 2)),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        OrderStatusEnum::NEW => 'جديد',
                        OrderStatusEnum::ACCEPTED => 'مقبول',
                        OrderStatusEnum::REJECTED => 'مرفوض',
                        OrderStatusEnum::PROCESSING => 'قيد المعالجة',
                        OrderStatusEnum::HIGH_PRIORITY_PROCESSING => 'معالجة ذات أولوية عالية',
                        OrderStatusEnum::NO_RESPONSE => 'لا يوجد رد',
                        OrderStatusEnum::INQUIRY => 'استفسار',
                        OrderStatusEnum::CANCELED => 'ملغى',
                        OrderStatusEnum::COMPLETED => 'مكتمل',
                        OrderStatusEnum::DOCUMENTS_DELIVERED => 'تم تسليم الوثائق',
                    ]),
                Tables\Filters\SelectFilter::make('liabilities')
                    ->label('الالتزامات')
                    ->options([
                        1 => 'نعم',
                        0 => 'لا',
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
