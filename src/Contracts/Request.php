<?php
namespace App\Contracts;

interface Request
{    
    /**
     * load
     * Загружает объект из GET или POST
     * @return Request
     */
    public static function load();
        
    /**
     * validate
     * Валидация
     * @return bool
     */
    public function validate();
}