<?php

namespace App\Http\Controllers;

use App\Http\Enums\UserType;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserCompleteResource;
use App\Models\LinkedSocialAccount;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Socialite;

class AuthController extends Controller
{
    /**
     * @return mixed
     */
    public function redirectToSteam()
    {
        return Socialite::driver("steam")->redirect();
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function steamCallback()
    {
        $socialiteUser = Socialite::driver("steam")->user();

        Log::info("Steam callback", [
            "Steam login callback" => $socialiteUser->user,
        ]);

        $linkedSocialAccount = LinkedSocialAccount::where("provider_id", $socialiteUser->id)
            ->first();

        if ($linkedSocialAccount) {
            $linkedSocialAccount->avatar = $socialiteUser->user["avatar"] ?? "";
            $linkedSocialAccount->save();

            return $this->getLoginSteamCallback($linkedSocialAccount->user->createToken("steam")->accessToken, $linkedSocialAccount->user->id);
        }

        if ($user = User::create([
            "username" => $socialiteUser->nickname,
            "type" => UserType::MEMBER,
        ])) {
            LinkedSocialAccount::create([
                "provider_id" => $socialiteUser->id,
                "user_id" => $user->id,
                "avatar" => $socialiteUser->user["avatar"] ?? "",
            ]);

            return $this->getLoginSteamCallback($user->createToken("steam")->accessToken, $user->id);
        }

        return redirect(config("app.url"));
    }

    /**
     * @return Response
     * TODO Feature test
     * TODO Add validation on client_callback
     */
    public function twitchSignedRoute(Request $request): Response
    {
        $route = URL::temporarySignedRoute("auth.twitch_authorize",
            now()->addHour(), [
                "user_id" => auth()->id(),
                "client_callback" => $request->client_callback,
            ]
        );

        return new Response([
            "data" => [
                "route" => $route
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function twitchAuthorize(Request $request)
    {
        session(["client_callback" => $request->client_callback]);
        $user = User::find($request->user_id);

        $stateUnique = false;
        while (!$stateUnique) {
            $state = Str::random(40);
            $stateUnique = User::whereTwitchState($state)->count() <= 0;
        }

        $user->twitch_state = $state;
        $user->save();

        return redirect("https://id.twitch.tv/oauth2/authorize?client_id=" . env("TWITCH_CLIENT_ID") . "&redirect_uri=" . route("auth.twitch_callback") . "&response_type=code&scope=user:read:subscriptions&state=" . $state);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function twitchCallback(Request $request)
    {
        if ($request->has("error"))
            return redirect(session("client_callback", env("CLIENT_URL")), 403);

        $response = Http::post("https://id.twitch.tv/oauth2/token", [
            "client_id" => env("TWITCH_CLIENT_ID"),
            "client_secret" => env("TWITCH_SECRET"),
            "redirect_uri" => route("auth.twitch_callback"),
            "code" => $request->code,
            "grant_type" => "authorization_code",
        ]);

        if ($response->status() != 200)
            return redirect(session("client_callback", env("CLIENT_URL")), 403);

        $userResponse = Http::withHeaders(["Client-Id" => env("TWITCH_CLIENT_ID")])->withToken($response["access_token"])->get("https://api.twitch.tv/helix/users");

        if ($userResponse->status() != 200)
            return redirect(session("client_callback", env("CLIENT_URL")), 403);

        $user = User::whereTwitchState($request->state)->first();
        $user->twitch_state = null;
        $user->twitch_scope = $request->scope;
        $user->twitch_id = $userResponse->json("data.0")["id"];
        $user->twitch_login = $userResponse->json("data.0")["login"];
        $user->save();

        return redirect(session("client_callback", env("CLIENT_URL")));
    }

    /**
     * @param $token
     * @param int $user_id
     * @return Application|RedirectResponse|Redirector
     */
    private function getLoginSteamCallback($token, int $user_id)
    {
        return redirect(env('CLIENT_URL') . "login/steam/callback/" . $user_id . "/" . $token);
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        User::create([
            "username" => $request->username,
            "password" => Hash::make($request->password),
            "esportal_username" => $request->esportal_username,
            "esportal_elo" => $request->esportal_elo,
        ]);

        if (!$client = User::getClient()) {
            return $this->getOAuthClientNotFound();
        }

        $data = User::getCredentials($client, ["username" => $request->username, "password" => $request->password]);

        return $this->getTokenResponse($data, 201);
    }

    /**
     * @return JsonResponse
     */
    private function getOAuthClientNotFound(): JsonResponse
    {
        return new JsonResponse([
            "error" => [
                "message" => "OAuth client not found in database.",
            ],
        ], 404);
    }

    /**
     * @param array $data
     * @param int $code
     * @return JsonResponse
     * @throws Exception
     */
    private function getTokenResponse(array $data, int $code): JsonResponse
    {
        $response = app()->handle(Request::create(route("passport.token"), "POST", $data));

        if ($response->getStatusCode() == 200) {
            $user = User::where("username", "LIKE", $data["username"])->first();
            return new JsonResponse(array_merge(json_decode($response->getContent(), true), ["user" => new UserCompleteResource($user)]), $code);
        }

        return new JsonResponse([
            "error" => [
                "message" => "The username or password is wrong.",
            ],
        ], $response->getStatusCode());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        if (!$client = User::getClient()) {
            return $this->getOAuthClientNotFound();
        }

        $credentials["username"] = $request->get("username");
        $credentials["password"] = $request->get("password");

        $data = User::getCredentials($client, $credentials);

        return $this->getTokenResponse($data, 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $accessToken = auth()->user()->tokens()->where("revoked", false)->first();

        if (!$accessToken)
            return new JsonResponse();

        DB::table("oauth_refresh_tokens")
            ->where("access_token_id", $accessToken->id)
            ->update([
                "revoked" => true,
            ]);

        $accessToken->revoke();

        return new JsonResponse();
    }

    /**
     * @return UserCompleteResource
     */
    public function status(): UserCompleteResource
    {
        return new UserCompleteResource(auth()->user());
    }
}
