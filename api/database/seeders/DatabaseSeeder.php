<?php

namespace Database\Seeders;

use App\Http\Enums\CasterRole;
use App\Http\Enums\ReleaseStatus;
use App\Http\Enums\TournamentUserState;
use App\Http\Enums\UserType;
use App\Models\Bracket;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = $this->createUser("admin", UserType::SUPERADMIN, "LizeeE");
        $streamer = $this->createUser("user", UserType::STREAMER, "growen");
        $streamer->twitch_id = "118368288";
        $streamer->twitch_login = "pg_cs";
        $streamer->save();

        $nocco = $this->createSponsor("Nocco");
        $steam = $this->createSponsor("Steam");
        $vikings = $this->createSponsor("Vikings");

        $singleElimination = $this->createBracket("Single Elimination", ReleaseStatus::RELEASED);
        $doubleElimination = $this->createBracket("Double Elimination", ReleaseStatus::RELEASED);
        $roundRobin = $this->createBracket("Round Robin");
        $swiss = $this->createBracket("Swiss");
        $twoStage = $this->createBracket("Two Stage");

        $friday = $this->createTournament("Fredagsrushen", $admin, $doubleElimination);
        $friday->live_at = Carbon::now()->addDays(8);
        $friday->registration_open_at = Carbon::now()->addDays(2);
        $friday->save();
        $saturday = $this->createTournament("Lördagsturren", $admin, $doubleElimination);
        $saturday->live_at = Carbon::now()->addDays(16);
        $saturday->registration_open_at = Carbon::now()->addDays(10);
        $saturday->save();
        $sunday = $this->createTournament("Söndagsbomben", $admin, $doubleElimination);
        $sunday->live_at = Carbon::now()->addDays(24);
        $sunday->registration_open_at = Carbon::now()->addDays(20);
        $sunday->save();

        $friday->coCasters()->attach($streamer, [
            "role" => CasterRole::MAIN_CASTER,
        ]);
        $saturday->coCasters()->attach($streamer, [
            "role" => CasterRole::MAIN_CASTER,
        ]);
        $sunday->coCasters()->attach($streamer, [
            "role" => CasterRole::MAIN_CASTER,
        ]);

        $friday->sponsors()->sync([$nocco->id, $steam->id]);
        $saturday->sponsors()->sync([$nocco->id, $vikings->id]);
        $sunday->sponsors()->sync([$steam->id, $vikings->id, $nocco->id]);

        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 999, "esportal_username" => "Brollan"]));

        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 1199, "esportal_username" => "Plopski"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 1399, "esportal_username" => "isak"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 1599, "esportal_username" => "phzy"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 1799, "esportal_username" => "peppzor"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 1999, "esportal_username" => "Dope"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => 2000, "esportal_username" => "sambarabam"]));

        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "h0rk"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "Cobretti1"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "KangenGG"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "Rallebree"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "rebko"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "Jabadabado"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "BadZap"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "RobinMos"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "LUNDINAz"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "Dah1b3rg"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "MagiskaAbbas"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "gogobossen"]));
        $this->attachUser($friday, User::factory()->create(["esportal_elo" => rand(500, 2500), "esportal_username" => "Skarrig"]));
    }

    /**
     * @param string $username
     * @param int $userType
     * @return User
     */
    private function createUser(string $username, int $userType, string $esportalUsername = null): User
    {
        if (!$user = User::where("username", $username)->first())
            $user = User::factory()->create([
                "username" => $username,
                "password" => Hash::make("password"),
                "type" => $userType,
                "streamer" => (bool)$userType == UserType::STREAMER,
                "esportal_username" => $esportalUsername,
            ]);

        return $user;
    }

    /**
     * @param string $title
     * @param User $user
     * @param Bracket $bracket
     * @return Tournament
     */
    private function createTournament(string $title, User $user, Bracket $bracket): Tournament
    {
        if (!$tournament = Tournament::where("title", $title)->first())
            $tournament = Tournament::factory()->create([
                "title" => $title,
                "user_id" => $user->id,
                "bracket_id" => $bracket->id,
            ]);
        else
            $tournament->update([
                "bracket_id" => $bracket->id,
            ]);

        return $tournament;
    }

    /**
     * @param string $title
     * @return Sponsor
     */
    private function createSponsor(string $title): Sponsor
    {
        if (!$sponsor = Sponsor::where("title", $title)->first())
            $sponsor = Sponsor::factory()->create([
                "title" => $title,
            ]);

        return $sponsor;
    }

    /**
     * @param string $title
     * @param int $status
     * @return Bracket
     */
    private function createBracket(string $title, int $status = ReleaseStatus::COMING_SOON): Bracket
    {
        if (!$bracket = Bracket::where("title", $title)->first()) {
            $bracket = Bracket::factory()->create([
                "title" => $title,
                "status" => $status,
            ]);
        } else {
            $bracket->update(["status" => $status]);
        }

        return $bracket;
    }

    /**
     * @param Tournament $tournament
     * @param User $user
     */
    private function attachUser(Tournament $tournament, User $user): void {
        $tournament->users()->attach($user, [
            "state" => TournamentUserState::CHECKED_IN,
            "order" => $tournament->last_order + 1,
        ]);
    }
}
