#!/bin/bash

# Script para eliminar trailing whitespaces y mejorar calidad de código
# Optimización para proyecto Blue Lagoon - Restaurante & Piscina

echo "🧹 Limpiando trailing whitespaces en archivos PHP y Blade..."

# Encuentra y limpia archivos PHP
find /home/sorak/restaurante-piscina/app -type f -name "*.php" -exec sed -i 's/[[:space:]]*$//' {} \;

# Encuentra y limpia archivos Blade
find /home/sorak/restaurante-piscina/resources/views -type f -name "*.blade.php" -exec sed -i 's/[[:space:]]*$//' {} \;

# Limpia archivos de configuración
find /home/sorak/restaurante-piscina/config -type f -name "*.php" -exec sed -i 's/[[:space:]]*$//' {} \;

# Limpia archivos de rutas
find /home/sorak/restaurante-piscina/routes -type f -name "*.php" -exec sed -i 's/[[:space:]]*$//' {} \;

echo "✅ Trailing whitespaces eliminados exitosamente"
echo ""
echo "📊 Verificando archivos procesados..."

# Cuenta archivos procesados
php_count=$(find /home/sorak/restaurante-piscina/app -type f -name "*.php" | wc -l)
blade_count=$(find /home/sorak/restaurante-piscina/resources/views -type f -name "*.blade.php" | wc -l)

echo "   - Archivos PHP procesados: $php_count"
echo "   - Archivos Blade procesados: $blade_count"
echo ""
echo "✨ Código limpio y optimizado!"
