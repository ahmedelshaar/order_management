<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

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

    public static function getPluralLabel(): ?string
    {
        return 'طلبات';
    }

    public static function getModelLabel(): string
    {
        return 'طلب';
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
                            ->disabled(fn () => auth()->user()->role === 1)
                            ->relationship('user', 'name', fn($query) => $query->where('role', 2))
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
                                ->suffixAction(Action::make('whatsapp')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->url(fn ($record) => "https://wa.me/966{$record->mobile_number}?text=عملينا العزيز ({$record->name}) الخيار السليم للسيارات ترحب بكم  نشكرك على تقديم طلب تمويل سيارة {$record->car_brand} {$record->car_name} برقم طلب {$record->id}")
                                    ->openUrlInNewTab()
                                    ->hidden(fn ($state) => empty($state))
                                )
                                ->required(),
                            Forms\Components\Select::make('is_saudi')
                                ->label('الجنسية')
                                ->options([
                                    1 => 'سعودي',
                                    0 => 'غير سعودي',
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
                            Forms\Components\Textarea::make('liabilities_amount')
                                ->label('قيمة الالتزامات')
                                ->columnSpanFull(),
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
                        Forms\Components\TextInput::make('car_model')
                            ->label('الموديل')
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Select::make('traffic_violations')
                            ->label('مخالفات المرور')
                            ->required()
                            ->options([
                                1 => 'نعم',
                                0 => 'لا',
                            ])
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم الطلب')
                    ->sortable(),
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
                Tables\Columns\IconColumn::make('is_saudi')
                    ->label('الجنسية')
                    ->boolean()
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
                Tables\Columns\TextColumn::make('car_model')
                    ->label('الموديل')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('traffic_violations')
                    ->label('مخالفات المرور')
                    ->boolean()
                    ->toggleable(),
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
                Tables\Filters\Filter::make('assigned_user')
                    ->label('تم تعيين مسؤول')
                    ->query(fn ($query) => $query->whereNotNull('user_id')),
                Tables\Filters\Filter::make('no_assigned_user')
                    ->label('بدون مسؤول')
                    ->default()
                    ->query(fn ($query) => $query->whereNull('user_id')),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('المسؤول')
                    ->preload()
                    ->searchable()
                    ->visible(fn () => auth()->user()->role == 2)
                    ->relationship('user', 'name', fn ($query) => $query->where('role', 2)),
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
                Tables\Filters\SelectFilter::make('traffic_violations')
                    ->label('مخالفات المرور')
                    ->options([
                        1 => 'نعم',
                        0 => 'لا',
                    ]),
            ])
            ->defaultSort('id', 'desc')
            ->poll('10s')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => auth()->user()->role === 2),
                    Tables\Actions\Action::make('whatsapp')
                        ->label('الواتساب')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn ($record) => "https://wa.me/966{$record->mobile_number}?text=عملينا العزيز ({$record->name}) الخيار السليم للسيارات ترحب بكم  نشكرك على تقديم طلب تمويل سيارة {$record->car_brand} {$record->car_name} برقم طلب {$record->id}")
                        ->openUrlInNewTab(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Assign')
                        ->icon('heroicon-o-user')
                        ->label('تعيين مسؤول')
                        ->modal()
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label('المسؤول')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->relationship('user', 'name', fn ($query) => $query->where('role', 2))
                        ])
                        ->requiresConfirmation()
                        ->action(function (Collection $records, array $data) {
                            $records->each(fn (Order $order) => $order->update(['user_id' => $data['user_id']]));
                        })
                        ->deselectRecordsAfterCompletion()
                ])
            ])
            ->checkIfRecordIsSelectableUsing(fn ($record) => $record->user_id == null);
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

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 2;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->role === 2;
    }

}
