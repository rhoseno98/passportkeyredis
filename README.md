# Passport Key Redis
- php artisan:keys-redis

Create the encryption keys for API authentication from redis storage

# Require Package
- Laravel Passport - dusterio/lumen-passport untuk lumen atau laravel/passport untuk laravel
- Laravel Redis - illuminate/redis dan predis/predis

# Cache
  sesuaikan .env
  CACHE_DRIVER = redis
  dan
  REDIS_HOST=<Redis Host>
  REDIS_PASSWORD=<Redis Password>
  REDIS_PORT=6379 <Redis Port>
