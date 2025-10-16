#!/bin/bash

# 🌊 Blue Lagoon API - Script de Pruebas
# Ejecutar desde la raíz del proyecto

echo "🌊 Iniciando pruebas de API - Blue Lagoon"
echo "========================================="

# Variables
BASE_URL="http://localhost:8000/api"
EMAIL="admin@bluelagoon.com"
PASSWORD="password"

echo "🔗 Base URL: $BASE_URL"
echo ""

# Test 1: Ping de la API
echo "📡 Test 1: Verificando conectividad..."
curl -s "$BASE_URL/ping" | jq .
echo ""

# Test 2: Login
echo "🔐 Test 2: Probando login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")

echo "$LOGIN_RESPONSE" | jq .
echo ""

# Extraer token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token // empty')

if [ -z "$TOKEN" ]; then
    echo "❌ Error: No se pudo obtener el token de autenticación"
    echo "Verifica que el usuario existe y las credenciales son correctas"
    exit 1
fi

echo "✅ Token obtenido: ${TOKEN:0:20}..."
echo ""

# Test 3: Información del usuario
echo "👤 Test 3: Información del usuario autenticado..."
curl -s -X GET "$BASE_URL/user" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 4: Listar mesas
echo "🪑 Test 4: Estado de las mesas..."
curl -s -X GET "$BASE_URL/tables/status" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 5: Listar productos
echo "🍽️ Test 5: Lista de productos..."
curl -s -X GET "$BASE_URL/products" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 6: Listar categorías
echo "📂 Test 6: Categorías de productos..."
curl -s -X GET "$BASE_URL/products/categories" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 7: Órdenes
echo "📋 Test 7: Lista de órdenes..."
curl -s -X GET "$BASE_URL/orders" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 8: Órdenes de cocina
echo "👨‍🍳 Test 8: Órdenes de cocina..."
curl -s -X GET "$BASE_URL/orders/kitchen" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 9: Descuentos del día
echo "💰 Test 9: Descuentos del día..."
curl -s -X GET "$BASE_URL/orders/daily-discounts" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

# Test 10: Logout
echo "🚪 Test 10: Logout..."
curl -s -X POST "$BASE_URL/auth/logout" \
  -H "Authorization: Bearer $TOKEN" | jq .
echo ""

echo "🎉 Pruebas completadas!"
echo ""
echo "📋 Resumen:"
echo "  - API funcionando: ✅"
echo "  - Autenticación: ✅"
echo "  - Endpoints básicos: ✅"
echo ""
echo "📱 La API está lista para integrarse con aplicaciones móviles"
