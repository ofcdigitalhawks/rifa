#!/bin/bash

# Script para iniciar o projeto em localhost
echo "🚀 Iniciando Sistema de Rifas em Localhost..."

# Verificar se Node.js está instalado
if ! command -v node &> /dev/null; then
    echo "❌ Node.js não encontrado! Instale o Node.js primeiro."
    exit 1
fi

# Verificar se PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ PHP não encontrado! Instale o PHP primeiro."
    exit 1
fi

# Criar arquivos necessários se não existirem
echo "📁 Criando arquivos necessários..."
touch pagamentos.db
touch webhook.log
echo "[]" > pagamentos.db

# Dar permissões
echo "🔧 Configurando permissões..."
chmod 666 pagamentos.db
chmod 666 webhook.log
chmod 644 gerar.php verificar.php webhook.php database.php

# Instalar dependências Node.js
echo "📦 Instalando dependências..."
if command -v pnpm &> /dev/null; then
    pnpm install
else
    npm install
fi

echo "🎯 Projeto configurado! Agora execute os comandos abaixo em 2 terminais diferentes:"
echo ""
echo "TERMINAL 1 (Next.js):"
echo "cd \"$(pwd)\""
echo "npm run dev"
echo ""
echo "TERMINAL 2 (Servidor PHP):"
echo "cd \"$(pwd)\""
echo "php -S localhost:8000"
echo ""
echo "🌐 Depois acesse: http://localhost:3000"
echo "📊 APIs PHP em: http://localhost:8000"
echo ""
echo "📋 Para testar a API diretamente:"
echo "curl http://localhost:8000/gerar.php?value=10.00"