<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\FightController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RuleTemplateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("v1")->group(
    function () {
        Route::apiResource("users", UserController::class)->only(["index"]);
        Route::apiResource("brackets", BracketController::class)->only(["index"]);
        Route::apiResource("sponsors", SponsorController::class)->only(["index"]);
        Route::apiResource("support_tickets", SupportTicketController::class)->only(["store"]);
        Route::apiResource("fights", FightController::class)->only(["index"]);
        Route::apiResource("rule_templates", RuleTemplateController::class)->only(["index"]);

        Route::apiResource("tournaments", TournamentController::class)->only(["index", "show"]);
        Route::get("/tournaments/{tournament}/general", [TournamentController::class, "show_general"])
            ->name("tournaments.show_general");
        Route::get("/tournaments/{tournament}/rules", [TournamentController::class, "show_rules"])
            ->name("tournaments.show_rules");
        Route::get("/tournaments/{tournament}/teams", [TournamentController::class, "show_teams"])
            ->name("tournaments.show_teams");
        Route::get("/tournaments/{tournament}/stats", [TournamentController::class, "show_stats"])
            ->name("tournaments.show_stats");

        Route::middleware(["guest"])->group(
            function () {
                Route::post("/register", [AuthController::class, "register"])->name("auth.register");
                Route::post("/login", [AuthController::class, "login"])->name("auth.login");
                Route::get("/login/steam", [AuthController::class, "redirectToSteam"])
                    ->name("auth.login.steam");
                Route::get("/callback/steam", [AuthController::class, "steamCallback"]);
            }
        );

        Route::middleware(["auth:api"])->group(
            function () {
                Route::apiResource("support_tickets", SupportTicketController::class)
                    ->only(["index", "show", "update"]);
                Route::post("/support_tickets/by_auth", [SupportTicketController::class, "store"])
                    ->name("support_tickets.store_by_auth");

                Route::get("/status", [AuthController::class, "status"])->name("auth.status");
                Route::post("/logout", [AuthController::class, "logout"])->name("auth.logout");
                Route::get("/twitch_signed_route", [AuthController::class, "twitchSignedRoute"])
                    ->name("auth.twitch_signed_route");

                Route::apiResource("users", UserController::class)->only(["update", "show"]);
                Route::put("/users/{user}/attach_sponsor", [UserController::class, "attach_sponsor"])
                    ->name("users.attach_sponsor");
                Route::put("/users/{user}/detach_sponsor", [UserController::class, "detach_sponsor"])
                    ->name("users.detach_sponsor");
                Route::get("/users/{user}/tournament_ids", [UserController::class, "tournament_ids"])
                    ->name("users.tournament_ids");
                Route::get("/users/{user}/tournament_status/{tournament}", [UserController::class, "status_by_tournament"])
                    ->name("users.status_by_tournament");
                Route::get("/users/{user}/tournament_status", [UserController::class, "status_all_tournaments"])
                    ->name("users.status_all_tournaments");
                Route::get("/users/{user}/check_twitch_subscriber", [UserController::class, "check_twitch_subscriber"])
                    ->name("users.check_twitch_subscriber");

                Route::apiResource("tournaments", TournamentController::class)
                    ->only(["store", "destroy", "update"]);
                Route::post("/tournaments/{tournament}/attachment", [TournamentController::class, "attachment"])
                    ->name("tournaments.attachment");
                Route::put("/tournaments/{tournament}/register", [TournamentController::class, "register"])
                    ->name("tournaments.register");
                Route::put("/tournaments/{tournament}/de_register", [TournamentController::class, "de_register"])
                    ->name("tournaments.de_register");
                Route::put("/tournaments/{tournament}/check_in", [TournamentController::class, "check_in"])
                    ->name("tournaments.check_in");
                Route::put("/tournaments/{tournament}/kick_player", [TournamentController::class, "kick_player"])
                    ->name("tournaments.kick_player");
                Route::put("/tournaments/{tournament}/open_registration", [TournamentController::class, "open_registration"])
                    ->name("tournaments.open_registration");
                Route::put("/tournaments/{tournament}/open_check_in", [TournamentController::class, "open_check_in"])
                    ->name("tournaments.open_check_in");
                Route::put("/tournaments/{tournament}/scramble_teams", [TournamentController::class, "scramble_teams"])
                    ->name("tournaments.scramble_teams");
                Route::put("/tournaments/{tournament}/attach_co_caster", [TournamentController::class, "attach_co_caster"])
                    ->name("tournaments.attach_co_caster");
                Route::put("/tournaments/{tournament}/detach_co_caster", [TournamentController::class, "detach_co_caster"])
                    ->name("tournaments.detach_co_caster");
                Route::put("/tournaments/{tournament}/switch_players", [TournamentController::class, "switch_players"])
                    ->name("tournaments.switch_players");
                Route::put("/tournaments/{tournament}/detach_from_teams", [TournamentController::class, "detach_from_teams"])
                    ->name("tournaments.detach_from_teams");
                Route::put("/tournaments/{tournament}/lock_player", [TournamentController::class, "lock_player"])
                    ->name("tournaments.lock_player");
                Route::put("/tournaments/{tournament}/unlock_player", [TournamentController::class, "unlock_player"])
                    ->name("tournaments.unlock_player");
                Route::put("/tournaments/{tournament}/add_player_to_team", [TournamentController::class, "add_player_to_team"])
                    ->name("tournaments.add_player_to_team");
                Route::get("/tournaments/{tournament}/actions", [TournamentController::class, "show_actions"])
                    ->name("tournaments.show_actions");

                Route::apiResource("brackets", BracketController::class)
                    ->only(["store"]);

                Route::apiResource("sponsors", SponsorController::class)
                    ->only(["store", "update", "destroy"]);
                Route::post("/sponsors/{sponsor}/attachment", [SponsorController::class, "attachment"])
                    ->name("sponsors.attachment");

                Route::apiResource("rule_templates", RuleTemplateController::class)
                    ->only(["store", "update", "destroy"]);

                Route::apiResource("prices", PriceController::class)
                    ->only(["store", "destroy", "update"]);
                Route::post("/prices/{price}/attachment", [PriceController::class, "attachment"])
                    ->name("prices.attachment");

                Route::apiResource("teams", TeamController::class)
                    ->only(["destroy", "update"]);
                Route::put("/teams/{team}/detach_player", [TeamController::class, "detach_player"])
                    ->name("teams.detach_player");

                Route::apiResource("notifications", NotificationController::class)
                    ->only(["index"]);

                Route::put("/fights/{fight}/set_winner", [FightController::class, "set_winner"])
                    ->name("fights.set_winner");
            }
        );

        Route::post("/search", [SearchController::class, "search"])->name("search");
    }
);
