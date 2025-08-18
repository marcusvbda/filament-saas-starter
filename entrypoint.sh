#!/bin/sh
set -e

echo "⏳ Aguardando banco de dados ficar disponível..."
until nc -z -v -w30 127.0.0.1 3306
do
  echo "Banco não disponível, esperando 5 segundos..."
  sleep 5
done

echo "🚀 Iniciando PHP-FPM..."
exec php-fpm
