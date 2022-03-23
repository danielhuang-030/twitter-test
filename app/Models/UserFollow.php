<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserFollow extends Pivot
{
    /**
     * updated at
     *
     * @var string
     */
    const UPDATED_AT = null;

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
     * set updated at
     *
     * @param mix $value
     *
     * @return self
     */
    public function setUpdatedAt($value): self
    {
        return $this;
    }
}
