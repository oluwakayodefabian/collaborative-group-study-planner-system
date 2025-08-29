<?php

namespace App\Traits;

use App\Models\WebPushSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;


trait HasWebPushSubscriptions
{
    public function webPushSubscriptions(): HasMany
    {
        return $this->hasMany(WebPushSubscription::class);
    }

    public function hasSubscriptions(): bool
    {
        return $this->webPushSubscriptions()->count() > 0;
    }
}
