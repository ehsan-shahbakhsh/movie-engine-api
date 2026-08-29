<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Filament\Resources\Movies\MovieResource;
use App\Filament\Resources\Series\SeriesResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('کاربر'),

                TextEntry::make('commentable_type')
                    ->label('مربوط به')
                    ->formatStateUsing(static function (string $state, $record) {
                        $title = $record->commentable->title ?? 'نامشخص';
                        $type = class_basename($state) === 'Movie' ? 'فیلم' : 'سریال';

                        return "$type: $title";
                    })
                    ->url(static fn($record) => class_basename($record->commentable_type) === 'Movie'
                        ? MovieResource::getUrl('view', ['record' => $record->commentable_id])
                        : SeriesResource::getUrl('view', ['record' => $record->commentable_id]),
                    )
                    ->color('primary'),

                TextEntry::make('parent.body')
                    ->label('در پاسخ به')
                    ->placeholder('کامنت اصلی')
                    ->limit()
                    ->columnSpanFull()
                    ->tooltip(static fn($state) => $state),

                TextEntry::make('body')
                    ->columnSpanFull()
                    ->label('متن کامنت'),

                IconEntry::make('is_official')
                    ->boolean()
                    ->label('رسمی'),

                IconEntry::make('is_spoiler')
                    ->boolean()
                    ->label('حاوی اسپویل'),

                TextEntry::make('status')
                    ->badge()
                    ->label('وضعیت'),

                TextEntry::make('replies_count')
                    ->badge()
                    ->color('info')
                    ->label('ریپلای‌ها'),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('تاریخ ایجاد')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('تاریخ آخرین بروزرسانی')
                    ->placeholder('-')
                    ->formatStateUsing(static fn($state) => verta($state)->formatDatetime()),
            ]);
    }
}
