<?php


namespace Rhoseno\PassportKeyRedis\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Passport;
use phpseclib\Crypt\RSA;

class GenerateKeyRedisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:keys-redis
                                      {--force : Overwrite keys they already exist}
                                      {--length=4096 : The length of the private key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the encryption keys for API authentication from redis storage';

    /**
     * Execute the console command.
     *
     * @param \phpseclib\Crypt\RSA $rsa
     * @return mixed
     */
    public function handle(RSA $rsa)
    {
        $keys = $rsa->createKey($this->input ? (int)$this->option('length') : 4096);

        list($publicPath, $privatePath, $publicKey, $privateKey) = [
            Passport::keyPath('oauth-public.key'),
            Passport::keyPath('oauth-private.key'),
            Cache::store('redis')->tags(['laravel_passport'])->get('PASSPORT_PUBLIC_KEY'),
            Cache::store('redis')->tags(['laravel_passport'])->get('PASSPORT_PRIVATE_KEY')
        ];

        if (($publicKey || $privateKey) && !$this->option('force')) {
            $this->error('Encryption keys already exist on redis. Use the --force option to overwrite them.');
        } else {
            Cache::store('redis')
                ->tags(['laravel_passport'])
                ->put('PASSPORT_PUBLIC_KEY', Arr::get($keys, 'publickey'));

            Cache::store('redis')
                ->tags(['laravel_passport'])
                ->put('PASSPORT_PRIVATE_KEY', Arr::get($keys, 'privatekey'));

            list($publicKey, $privateKey) = [
                Cache::store('redis')->tags(['laravel_passport'])->get('PASSPORT_PUBLIC_KEY'),
                Cache::store('redis')->tags(['laravel_passport'])->get('PASSPORT_PRIVATE_KEY')
            ];
        }

        file_put_contents($publicPath, $publicKey);
        file_put_contents($privatePath, $privateKey);

        $this->info('Encryption keys saved to storage folder successfully.');
    }
}