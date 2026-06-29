#!/bin/sh

# Tulis .env dari environment variables Railway
cat > /app/.env << EOF
APP_NAME="${APP_NAME:-SMART}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=single
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BCRYPT_ROUNDS=12

MAIL_MAILER=log
MAIL_FROM_ADDRESS="admin@smart-rt.com"
MAIL_FROM_NAME="SMART"
EOF

echo "=== .env created ==="
echo "DB_HOST: ${DB_HOST}"
echo "DB_DATABASE: ${DB_DATABASE}"

php artisan config:clear
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
