<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomUserProvider extends EloquentUserProvider
{
    protected $userRepository;

    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @param  \App\Repositories\Contracts\UserRepositoryInterface  $userRepository
     * @return void
     */
    public function __construct(
        HasherContract $hasher,
        $model,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($hasher, $model);
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
             array_key_exists('password', $credentials))) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            // Handle login field (email, phone, or username)
            if ($key === 'login') {
                $query->where(function ($q) use ($value) {
                    $q->where('email', $value)
                      ->orWhere('phone', $value)
                      ->orWhere('username', $value);
                });
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->userRepository->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(UserContract $user, array $credentials, bool $force = false)
    {
        if (! $this->hasher->needsRehash($user->getAuthPassword()) && ! $force) {
            return;
        }

        $user->forceFill([
            $user->getAuthPasswordName() => $this->hasher->make($credentials['password']),
        ])->save();
    }

    /**
     * Retrieve a user by email, phone, or username.
     *
     * @param  string  $login
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByLogin($login)
    {
        return $this->userRepository->findByLogin($login);
    }

    /**
     * Check if user exists by email, phone, or username.
     *
     * @param  string  $login
     * @return bool
     */
    public function userExists($login): bool
    {
        return $this->userRepository->findByLogin($login) !== null;
    }

    /**
     * Get user by email.
     *
     * @param  string  $email
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get user by phone.
     *
     * @param  string  $phone
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getByPhone($phone)
    {
        return $this->userRepository->findByPhone($phone);
    }

    /**
     * Get user by username.
     *
     * @param  string  $username
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getByUsername($username)
    {
        return $this->userRepository->findByUsername($username);
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newModelQuery($model = null)
    {
        return is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();
    }
}
