<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function getUserFollowedAuthorsByUserIdAndAuthorIds(int $userId, array $authorIds): Collection
    {
        $user = $this->model->query()
        ->with([
            'following' => function ($query) use ($authorIds) {
                $query->whereIn('follow_id', $authorIds);
            },
        ])->find($userId);

        return empty($user) ? Collection::make() : $user->following;
    }

    public function getByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    protected function model(): string
    {
        return User::class;
    }
}
