<?php

namespace App\Models;

use Laravel\Sanctum\Contracts\HasAbilities;
use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessToken extends Model implements HasAbilities
{
    use \MongoDB\Laravel\Eloquent\HybridRelations;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'personal_access_tokens';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_type',
        'tokenable_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function tokenable()
    {
        return $this->morphTo();
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability)
    {
        return in_array('*', $this->abilities) ||
               array_key_exists($ability, array_flip($this->abilities));
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function cant($ability)
    {
        return ! $this->can($ability);
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function cannot($ability)
    {
        return $this->cant($ability);
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', hash('sha256', $token))->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }

        return null;
    }

    /**
     * Determine if the token has expired.
     *
     * @return bool
     */
    public function hasExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the token's last used timestamp.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getLastUsedAt()
    {
        return $this->last_used_at;
    }

    /**
     * Update the token's last used timestamp.
     *
     * @return void
     */
    public function updateLastUsedAt()
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }

    /**
     * Get the current access token being used by the request.
     *
     * @return static|null
     */
    public static function currentAccessToken()
    {
        return app('request')->attributes->get('sanctum_token');
    }

    /**
     * Revoke the token.
     *
     * @return bool|null
     */
    public function revoke()
    {
        return $this->delete();
    }
}
