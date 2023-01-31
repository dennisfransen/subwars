<?php

namespace App\Providers;

use App\Models\Bracket;
use App\Models\SupportTicket;
use App\Models\Tournament;
use App\Models\User;
use App\Policies\BracketPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\TournamentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Tournament::class => TournamentPolicy::class,
        Bracket::class => BracketPolicy::class,
        User::class => UserPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
    }
}
