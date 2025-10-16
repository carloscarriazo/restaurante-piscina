#!/bin/bash

# ==================================
# BLUE LAGOON RESTAURANT
# Script de Deployment a Producción
# ==================================

set -e  # Salir si hay algún error

echo "===================================="
echo "🚀 DEPLOYMENT - BLUE LAGOON RESTAURANT"
echo "===================================="
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# 1. Verificar pre-requisitos
echo "1. Verificando pre-requisitos..."
echo "===================================="

# Verificar PHP
if ! command -v php &> /dev/null; then
    print_error "PHP no está instalado"
    exit 1
fi
print_success "PHP $(php -v | head -n 1 | cut -d ' ' -f 2) instalado"

# Verificar Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer no está instalado"
    exit 1
fi
print_success "Composer instalado"

# Verificar Node.js
if ! command -v node &> /dev/null; then
    print_warning "Node.js no está instalado (recomendado para assets)"
else
    print_success "Node.js $(node -v) instalado"
fi

echo ""

# 2. Modo mantenimiento
echo "2. Activando modo mantenimiento..."
echo "===================================="
php artisan down --render="errors::503" --secret="deployment-secret-key"
print_success "Modo mantenimiento activado"
echo ""

# 3. Git pull
echo "3. Obteniendo últimos cambios..."
echo "===================================="
if [ -d ".git" ]; then
    git pull origin main
    print_success "Cambios obtenidos de Git"
else
    print_warning "No es un repositorio Git"
fi
echo ""

# 4. Instalar dependencias
echo "4. Instalando dependencias..."
echo "===================================="
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Dependencias de Composer instaladas"

if command -v npm &> /dev/null; then
    npm ci --production
    print_success "Dependencias de NPM instaladas"
fi
echo ""

# 5. Limpiar cachés
echo "5. Limpiando cachés antiguos..."
echo "===================================="
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
print_success "Cachés limpiados"
echo ""

# 6. Migrar base de datos
echo "6. Ejecutando migraciones..."
echo "===================================="
read -p "¿Ejecutar migraciones? (s/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    php artisan migrate --force
    print_success "Migraciones ejecutadas"
else
    print_warning "Migraciones omitidas"
fi
echo ""

# 7. Optimizar aplicación
echo "7. Optimizando aplicación..."
echo "===================================="
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
print_success "Aplicación optimizada"
echo ""

# 8. Compilar assets
echo "8. Compilando assets..."
echo "===================================="
if command -v npm &> /dev/null; then
    npm run build
    print_success "Assets compilados"
else
    print_warning "NPM no disponible, assets no compilados"
fi
echo ""

# 9. Permisos
echo "9. Configurando permisos..."
echo "===================================="
chmod -R 775 storage bootstrap/cache
print_success "Permisos configurados"
echo ""

# 10. Generar documentación Swagger
echo "10. Generando documentación API..."
echo "===================================="
php artisan l5-swagger:generate
print_success "Documentación Swagger generada"
echo ""

# 11. Verificar estado
echo "11. Verificando estado de la aplicación..."
echo "===================================="
php artisan about
echo ""

# 12. Desactivar modo mantenimiento
echo "12. Desactivando modo mantenimiento..."
echo "===================================="
php artisan up
print_success "Aplicación en línea"
echo ""

# 13. Limpiar logs antiguos
echo "13. Limpiando logs antiguos..."
echo "===================================="
find storage/logs -name "*.log" -type f -mtime +30 -delete
print_success "Logs antiguos eliminados"
echo ""

echo "===================================="
echo "✅ DEPLOYMENT COMPLETADO EXITOSAMENTE"
echo "===================================="
echo ""
echo "🔗 Accesos:"
echo "   - Aplicación: ${APP_URL}"
echo "   - API Docs: ${APP_URL}/api/documentation"
echo ""
echo "📊 Siguiente pasos:"
echo "   1. Verificar que la aplicación esté funcionando"
echo "   2. Revisar logs: tail -f storage/logs/laravel.log"
echo "   3. Monitorear rendimiento"
echo "   4. Hacer pruebas de las funcionalidades clave"
echo ""
