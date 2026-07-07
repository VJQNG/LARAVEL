#!/bin/bash
# Script para generar secretos criptográficamente seguros
echo "🔒 Inicializando bóveda de secretos..."
mkdir -p docker/secrets

# Generar contraseñas aleatorias en base64
openssl rand -base64 32 > docker/secrets/db_password.txt
echo "base64:$(openssl rand -base64 32)" > docker/secrets/app_key.txt

echo "✅ Secretos inyectados en docker/secrets/ de forma segura."
