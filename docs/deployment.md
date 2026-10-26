# Deployment

1. Set .env (APP_URL, DB, tenancy vars).
2. composer install, npm install && npm run build.
3. php artisan migrate --force.
4. php artisan config:cache, route:cache, view:cache.
5. Queue worker for jobs (php artisan queue:work).
6. Configure web server to point to public/.
