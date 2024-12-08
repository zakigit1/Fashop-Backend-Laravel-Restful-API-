<?php

namespace App\Providers;

use App\Models\EmailSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /** Set SMTP Mail Configuration :  */
        $this->emailConfiguration();
        // dd(Config::get('mail'));

        // Set MySQL timezone to match application timezone
        // try {
        //     DB::statement("SET time_zone = ?", [config('app.timezone')]);
        // } catch (\Exception $e) {
        //     DB::statement("SET time_zone = '+01:00'");
        // }
    }








    private function emailConfiguration(): void
    {
        if(Schema::hasTable('email_settings')){
            $EmailConfig = EmailSetting::first();

            Config::set('mail.mailers.smtp.host',$EmailConfig->host ?? env('MAIL_HOST', 'smtp.mailgun.org'));
            Config::set('mail.mailers.smtp.port',$EmailConfig->port ?? env('MAIL_PORT', 587)); 
            Config::set('mail.mailers.smtp.encryption',$EmailConfig->encryption ?? env('MAIL_ENCRYPTION', 'tls') );
            Config::set('mail.mailers.smtp.username',$EmailConfig->username ?? env('MAIL_USERNAME') );
            Config::set('mail.mailers.smtp.password',$EmailConfig->password ?? env('MAIL_PASSWORD'));
            Config::set('mail.from.address',$EmailConfig->email ?? env('MAIL_FROM_ADDRESS', 'hello@example.com'));
            Config::set('mail.from.name',$EmailConfig->name ?? env('MAIL_FROM_NAME', 'Example'));
        }else{
            Config::set('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.mailgun.org'));
            Config::set('mail.mailers.smtp.port',env('MAIL_PORT', 587)); 
            Config::set('mail.mailers.smtp.encryption',env('MAIL_ENCRYPTION', 'tls') );
            Config::set('mail.mailers.smtp.username',env('MAIL_USERNAME') );
            Config::set('mail.mailers.smtp.password',env('MAIL_PASSWORD'));
            Config::set('mail.from.address',env('MAIL_FROM_ADDRESS', 'hello@example.com'));
            Config::set('mail.from.name',env('MAIL_FROM_NAME', 'Example'));
        }
    }
}
