<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserFollow extends Pivot
{
    /**
     * updated at.
     *
     * @var string
     */
    public const UPDATED_AT = null;

    /**
     * user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'follow_id');
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string|null
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }
}
