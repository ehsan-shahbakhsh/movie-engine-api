<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Enums\CommentStatus;
use App\Filament\Resources\Movies\MovieResource;
use App\Filament\Resources\Series\SeriesResource;
use App\Filament\Shared\FormComponents;
use App\Filament\Shared\TableColumns;
use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TableColumns::id(),

                TableColumns::name('user.name', 'کاربر'),

                TextColumn::make('commentable_type')
                    ->label('مربوط به')
                    ->formatStateUsing(static function (string $state, Comment $record) {
                        $title = $record->commentable->title ?? 'نامشخص';
                        $type = class_basename($state) === 'Movie' ? 'فیلم' : 'سریال';

                        return "$type: $title";
                    })
                    ->url(static fn($record) => class_basename($record->commentable_type) === 'Movie'
                        ? MovieResource::getUrl('view', ['record' => $record->commentable_id])
                        : SeriesResource::getUrl('view', ['record' => $record->commentable_id]),
                    )
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('parent.body')
                    ->label('در پاسخ به')
                    ->limit(30)
                    ->tooltip(static fn($state) => $state)
                    ->placeholder('کامنت اصلی')
                    ->description(static fn(Comment $record) => $record->parent_id ? 'شناسه والد: ' . $record->parent_id : '')
                    ->color(static fn(Comment $record) => $record->parent_id ? 'gray' : 'primary')
                    ->toggleable(),

                TextColumn::make('body')
                    ->label('متن کامنت')
                    ->limit(50)
                    ->tooltip(static fn($state) => $state)
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_official')
                    ->boolean()
                    ->label('رسمی')
                    ->toggleable(),

                IconColumn::make('is_spoiler')
                    ->boolean()
                    ->label('حاوی اسپویل')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('وضعیت')
                    ->toggleable(),

                TextColumn::make('replies_count')
                    ->label('ریپلای‌ها')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TableColumns::createdAt(),
                TableColumns::updatedAt(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->label('وضعیت')
                    ->options(CommentStatus::class),

                TernaryFilter::make('is_spoiler')
                    ->label('وضعیت اسپویل')
                    ->placeholder('همه کامنت‌ها')
                    ->trueLabel('حاوی اسپویل')
                    ->falseLabel('بدون اسپویل'),

                TernaryFilter::make('is_official')
                    ->label('وضعیت رسمی بودن')
                    ->placeholder('همه موارد')
                    ->trueLabel('فقط رسمی')
                    ->falseLabel('فقط غیررسمی'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('تأیید')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(static function (Comment $record) {
                            $record->update(['status' => CommentStatus::Approved]);
                        })
                        ->hidden(static fn(Comment $record) => $record->status === CommentStatus::Approved)
                        ->successNotificationTitle('نظر با موفقیت تأیید شد.'),

                    Action::make('reject')
                        ->label('رد کردن')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('رد کردن نظر')
                        ->modalDescription('آیا از رد کردن این نظر اطمینان دارید؟ در این صورت، کامنت در سایت نمایش داده نخواهد شد.')
                        ->modalSubmitActionLabel('بله، رد شود')
                        ->action(static function (Comment $record) {
                            $record->update(['status' => CommentStatus::Rejected]);
                        })
                        ->hidden(static fn(Comment $record) => $record->status === CommentStatus::Rejected)
                        ->successNotificationTitle('نظر با موفقیت رد شد.'),

                    Action::make('reply')
                        ->label('پاسخ دادن')
                        ->icon(Heroicon::OutlinedArrowUturnLeft)
                        ->color('info')
                        ->schema([
                            Textarea::make('body')
                                ->label('متن پاسخ')
                                ->placeholder('پاسخ خود را اینجا بنویسید...')
                                ->required()
                                ->rows(4)
                                ->maxLength(1000),

                            FormComponents::status('is_spoiler', 'حاوی اسپویل')
                                ->default(false),
                        ])
                        ->action(static function (Comment $record, array $data) {
                            $record->replies()->create([
                                'user_id' => auth()->id(),
                                'commentable_type' => $record->commentable_type,
                                'commentable_id' => $record->commentable_id,
                                'body' => $data['body'],
                                'status' => CommentStatus::Approved,
                                'is_official' => true,
                                'is_spoiler' => $data['is_spoiler'],
                            ]);
                        })
                        ->successNotificationTitle('پاسخ شما با موفقیت ثبت شد.'),

                    ViewAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
