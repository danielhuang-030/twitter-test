<?php

namespace App\Enums;

enum ApiResponseCode: string
{
    case SUCCESS = '000000';

    case ERROR_UNEXPECTED = '999999';
    case ERROR_VALIDATION = '999001';
    case ERROR_UNAUTHORIZED = '999002';

    case ERROR_USER_NOT_EXIST = '500001';
    case ERROR_USER_ADD = '500002';

    case ERROR_POST_NOT_EXIST = '501001';
    case ERROR_POST_ADD = '501002';
    case ERROR_POST_EDIT = '501003';
    case ERROR_POST_DEL = '501004';
    case ERROR_POST_LIKE = '501005';
    case ERROR_POST_DISLIKE = '501006';

    case ERROR_FOLLOW = '502001';
    case ERROR_FOLLOW_SELF = '502002';
    case ERROR_FOLLOW_HAVE_FOLLOWED = '502003';

    case ERROR_UNFOLLOW = '503001';
    case ERROR_UNFOLLOW_SELF = '503002';
    case ERROR_UNFOLLOW_NOT_FOLLOWED = '503003';

    public function message(): string
    {
        return match ($this) {
            static::SUCCESS => 'Success.',

            static::ERROR_UNEXPECTED => 'Unexpected error.',
            static::ERROR_VALIDATION => 'Validation error.',
            static::ERROR_UNAUTHORIZED => 'Unauthorized.',

            static::ERROR_USER_NOT_EXIST => 'User does not exist.',
            static::ERROR_USER_ADD => 'User add failed.',

            static::ERROR_POST_NOT_EXIST => 'Post does not exist.',
            static::ERROR_POST_ADD => 'Post add failed.',
            static::ERROR_POST_EDIT => 'Post update failed.',
            static::ERROR_POST_DEL => 'Post delete failed.',
            static::ERROR_POST_LIKE => 'Post like failed.',
            static::ERROR_POST_DISLIKE => 'Post dislike failed.',

            static::ERROR_FOLLOW => 'Following failed.',
            static::ERROR_FOLLOW_SELF => 'Can not follow yourself.',
            static::ERROR_FOLLOW_HAVE_FOLLOWED => 'You have followed.',

            static::ERROR_UNFOLLOW => 'Unfollow failed.',
            static::ERROR_UNFOLLOW_SELF => 'Can not unfollow yourself.',
            static::ERROR_UNFOLLOW_NOT_FOLLOWED => 'You have not followed.',

            default => 'Unexpected code.',
        };
    }
}
