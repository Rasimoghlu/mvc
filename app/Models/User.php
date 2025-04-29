<?php

namespace App\Models;

use Src\Facades\Model;

class User extends Model
{
    protected string $table = 'users';
    
    /**
     * Indica se o modelo deve usar timestamps
     * 
     * @var bool
     */
    protected bool $timestamps = true;
    
    /**
     * Nome do campo de atualização
     * 
     * @var string
     */
    protected string $updatedField = 'updated_at';
    
    /**
     * Nome do campo de criação
     * 
     * @var string
     */
    protected string $createdField = 'created_at';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'name',
        'email',
        'password'
    ];
}