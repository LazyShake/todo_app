<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово заполнять.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
    ];

    /**
     * Атрибуты, которые должны быть скрыты в JSON-ответах.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Автоматическое приведение типов для атрибутов.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Генерация уникального API-токена.
     */
    public function generateApiToken()
    {
        $this->api_token = hash('sha256', $this->id . $this->email . $this->password . time());
        $this->save();
    }

    /**
     * Связь "один ко многим" — у пользователя есть много задач.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
