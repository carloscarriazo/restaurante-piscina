#!/bin/bash

# Script de Verificación del Sistema - Blue Lagoon Restaurant
# Verifica que todos los componentes principales estén funcionando

echo "🔍 VERIFICACIÓN DEL SISTEMA - BLUE LAGOON RESTAURANT"
echo "=================================================="
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Contador de checks
PASSED=0
FAILED=0

# Función para verificar
check() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $1"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $1"
        ((FAILED++))
    fi
}

# 1. Verificar PHP
echo "📋 Verificando PHP..."
php -v > /dev/null 2>&1
check "PHP instalado"

# 2. Verificar Composer
echo ""
echo "📦 Verificando Composer..."
composer --version > /dev/null 2>&1
check "Composer instalado"

# 3. Verificar archivo .env
echo ""
echo "⚙️  Verificando configuración..."
if [ -f .env ]; then
    check ".env existe"
else
    echo -e "${RED}✗${NC} .env existe"
    echo -e "${YELLOW}  💡 Copia .env.example a .env${NC}"
    ((FAILED++))
fi

# 4. Verificar base de datos
echo ""
echo "🗄️  Verificando base de datos..."
if [ -f database/database.sqlite ]; then
    check "Base de datos SQLite existe"
else
    echo -e "${RED}✗${NC} Base de datos SQLite existe"
    echo -e "${YELLOW}  💡 Ejecuta: touch database/database.sqlite && php artisan migrate${NC}"
    ((FAILED++))
fi

# 5. Verificar vendor
echo ""
echo "📚 Verificando dependencias..."
if [ -d vendor ]; then
    check "Dependencias PHP instaladas"
else
    echo -e "${RED}✗${NC} Dependencias PHP instaladas"
    echo -e "${YELLOW}  💡 Ejecuta: composer install${NC}"
    ((FAILED++))
fi

# 6. Verificar node_modules
if [ -d node_modules ]; then
    check "Dependencias Node instaladas"
else
    echo -e "${YELLOW}⚠${NC} Dependencias Node no instaladas"
    echo -e "${YELLOW}  💡 Ejecuta: npm install${NC}"
fi

# 7. Verificar L5-Swagger
echo ""
echo "📖 Verificando Swagger/OpenAPI..."
if [ -f config/l5-swagger.php ]; then
    check "Configuración Swagger existe"
else
    echo -e "${RED}✗${NC} Configuración Swagger existe"
    echo -e "${YELLOW}  💡 Ejecuta: composer require darkaonline/l5-swagger${NC}"
    ((FAILED++))
fi

# 8. Verificar documentación generada
if [ -f storage/api-docs/api-docs.json ]; then
    check "Documentación Swagger generada"
else
    echo -e "${YELLOW}⚠${NC} Documentación Swagger no generada"
    echo -e "${YELLOW}  💡 Ejecuta: php artisan l5-swagger:generate${NC}"
fi

# 9. Verificar archivos críticos
echo ""
echo "📁 Verificando archivos críticos..."
CRITICAL_FILES=(
    "app/Http/Controllers/Controller.php"
    "app/Http/Controllers/Api/AuthController.php"
    "routes/api.php"
    "routes/web.php"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        check "Existe: $file"
    else
        echo -e "${RED}✗${NC} Existe: $file"
        ((FAILED++))
    fi
done

# 10. Verificar migraciones
echo ""
echo "🔧 Verificando migraciones..."
MIGRATION_COUNT=$(ls -1 database/migrations/*.php 2>/dev/null | wc -l)
if [ $MIGRATION_COUNT -gt 0 ]; then
    check "Migraciones encontradas ($MIGRATION_COUNT archivos)"
else
    echo -e "${RED}✗${NC} Migraciones encontradas"
    ((FAILED++))
fi

# 11. Verificar modelos principales
echo ""
echo "🏗️  Verificando modelos..."
MODELS=(
    "app/Models/User.php"
    "app/Models/Order.php"
    "app/Models/Product.php"
    "app/Models/Table.php"
)

MODEL_COUNT=0
for model in "${MODELS[@]}"; do
    if [ -f "$model" ]; then
        ((MODEL_COUNT++))
    fi
done

if [ $MODEL_COUNT -eq ${#MODELS[@]} ]; then
    check "Modelos principales ($MODEL_COUNT/${#MODELS[@]})"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} Modelos principales ($MODEL_COUNT/${#MODELS[@]})"
fi

# 12. Verificar servicios
echo ""
echo "⚙️  Verificando servicios..."
SERVICE_COUNT=$(ls -1 app/Services/*.php 2>/dev/null | wc -l)
if [ $SERVICE_COUNT -gt 0 ]; then
    check "Servicios implementados ($SERVICE_COUNT archivos)"
else
    echo -e "${YELLOW}⚠${NC} Servicios implementados"
fi

# Resumen
echo ""
echo "=================================================="
echo "📊 RESUMEN DE VERIFICACIÓN"
echo "=================================================="
echo -e "✅ Pasados: ${GREEN}$PASSED${NC}"
echo -e "❌ Fallados: ${RED}$FAILED${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 ¡SISTEMA VERIFICADO CORRECTAMENTE!${NC}"
    echo ""
    echo "🚀 Para iniciar el servidor:"
    echo "   php artisan serve"
    echo ""
    echo "📖 URLs importantes:"
    echo "   Principal:     http://localhost:8000"
    echo "   Swagger Docs:  http://localhost:8000/api/documentation"
    echo "   Cocina:        http://localhost:8000/kitchen"
    echo "   Meseros:       http://localhost:8000/waiter"
    echo ""
else
    echo -e "${RED}⚠️  SE ENCONTRARON $FAILED PROBLEMAS${NC}"
    echo ""
    echo "💡 Revisa los mensajes anteriores para solucionar los problemas"
    echo ""
fi

# Información adicional
echo "📚 Documentación disponible:"
echo "   - README.md"
echo "   - EVALUACION_COMPLETA_PROYECTO.md"
echo "   - GUIA_SWAGGER.md"
echo "   - RESUMEN_EJECUTIVO.md"
echo ""

exit $FAILED
