<?php

namespace App\Constants;

class ValidationRules
{
    // Reglas comunes de string
    public const REQUIRED_STRING_255 = 'required|string|max:255';
    public const NULLABLE_STRING_255 = 'nullable|string|max:255';
    public const NULLABLE_STRING_500 = 'nullable|string|max:500';
    public const NULLABLE_STRING_1000 = 'nullable|string|max:1000';
    public const NULLABLE_STRING = 'nullable|string';

    // Reglas de email
    public const REQUIRED_EMAIL_UNIQUE = 'required|email|unique:users,email';

    // Reglas numéricas
    public const NULLABLE_INTEGER_MIN_ZERO = 'nullable|integer|min:0';
    public const NULLABLE_NUMERIC_MIN_0 = 'nullable|numeric|min:0';
    public const REQUIRED_NUMERIC_MIN_0 = 'required|numeric|min:0';
    public const MIN_ZERO = 'min:0';

    // Reglas de fecha
    public const DATE_FORMAT_YMD = 'nullable|date_format:Y-m-d';
    public const DATE_FORMAT_YMD_HIS = 'Y-m-d H:i:s';

    // Reglas de decimal
    public const DECIMAL_TWO = 'decimal:2';
    public const DECIMAL_THREE = 'decimal:3';

    // Reglas de tamaño de producto
    public const SIZE_250GR = '250gr';
    public const SIZE_160GR = '160gr';
}
