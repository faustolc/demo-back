<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasApiTokens;
    use Notifiable;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'picture_profile',
        'phone',
        'password',
        'role_ids',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    protected $with = ['roles'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var list<string>
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the user's profile picture URL.
     *
     * @return string
     */
    public function getPictureProfileAttribute($value)
    {
        if ($value) {
            // Genera una URL pública y permanente para el archivo en S3
            return Storage::disk('s3')->url($value);
        }

        // Retorna una URL por defecto si el usuario no tiene foto
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tokens()
    {
        return $this->morphMany(\App\Models\PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @param  \DateTimeInterface|null  $expiresAt
     * @return object
     */
    public function createToken(string $name, array $abilities = ['*'], \DateTimeInterface $expiresAt = null)
    {
        // Generate plain text token
        $plainTextToken = \Illuminate\Support\Str::random(40);

        // Create token record directly
        $token = new \App\Models\PersonalAccessToken([
            'tokenable_type' => get_class($this),
            'tokenable_id' => $this->getKey(),
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        $token->save();

        // Return a simple object with the same interface as NewAccessToken
        return (object) [
            'accessToken' => $token,
            'plainTextToken' => $plainTextToken,
        ];
    }
}
