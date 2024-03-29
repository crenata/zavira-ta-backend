<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserModel extends BaseAuthenticatableModel implements MustVerifyEmail {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "email",
        "token",
        "password",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "token",
        "password",
        "remember_token",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "email_verified_at" => "datetime",
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/users/" . $this->attributes["image"];
    }
}
