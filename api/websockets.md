# 1 Consume events

## 1.1 Subscribe to public channel

When there is no authorization demanded, we use public channels. Subscribe to public channel and event by using echo
method listen().

```javascript
echo().listen('{CHANNEL_NAME}', '{EVENT_NAME}', (e) => {
})
```

# 2 Channels

## 2.1 App.Models.Tournament

A public channel of any tournament.

## 2.2 App.Models.Tournament.{TOURNAMENT_ID}

A public channel of a certain tournament.

## 2.2 App.Models.User.{USER_ID}

A public channel of a certain user.

# 3 Events

## 3.1 TournamentCreatedEvent

Submitted when a tournament gets created by any streamer.

### Channels

- App.Models.Tournament
- App.Models.User.{STREAMER_ID}

### Payload

```json
{
    "tournament": {
        "id": 1,
        "title": "Tournament title",
        "max_teams": 32,
        "live_at": "2021-12-31 17:00:00",
        "is_visible": true,
        "is_open_for_registration": false,
        "is_open_for_check_in": false
    }
}
```

## 3.2 TournamentUpdatedEvent

Submitted when any tournament gets updated. This occurs when any of the attributes of the payload gets altered.

### Channels

- App.Models.Tournament
- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "tournament": {
        "id": 1,
        "title": "Tournament title",
        "max_teams": 32,
        "live_at": "2021-12-31 17:00:00",
        "is_visible": true,
        "is_open_for_registration": false,
        "is_open_for_check_in": false,
        "registered": [
            {
                "id": 1,
                "username": "user",
                "elo": 1933,
                "is_streamer": false,
                "checked_in": true
            }
        ]
    }
}
```

## 3.3 TournamentDestroyedEvent

Submitted when a tournament gets deleted by any streamer.

### Channels

- App.Models.Tournament
- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{STREAMER_ID}

### Payload

```json
{
    "tournament": {
        "id": 1,
        "title": "Tournament title",
        "max_teams": 32,
        "live_at": "2021-12-31 17:00:00",
        "is_visible": true,
        "is_open_for_registration": false,
        "is_open_for_check_in": false
    }
}
```

## 3.4 TournamentOpenedForRegistrationEvent

Submitted when a tournament gets opened for registration by any streamer.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "tournament": {
        "id": 1,
        "title": "Tournament title",
        "max_teams": 32,
        "live_at": "2021-12-31 17:00:00",
        "is_visible": true,
        "is_open_for_registration": false,
        "is_open_for_check_in": false
    }
}
```

## 3.5 TournamentOpenedForCheckInEvent

Submitted when a tournament gets opened for check in by any streamer.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{REGISTERED_USER_ID}

### Payload

```json
{
    "tournament": {
        "id": 1,
        "title": "Tournament title",
        "max_teams": 32,
        "live_at": "2021-12-31 17:00:00",
        "is_visible": true,
        "is_open_for_registration": false,
        "is_open_for_check_in": false
    }
}
```

## 3.6 UserRegisteredToTournamentEvent

Submitted when a user registers to a tournament.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "data": {
        "user": {
            "id": 1,
            "username": "admin",
            "esportal_username": null,
            "is_streamer": false,
            "is_live": false,
            "steam_avatar": "https:\/\/steamcdn-a.akamaihd.net\/steamcommunity\/public\/images\/avatars\/fe\/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
            "steam_avatar_medium": "https:\/\/steamcdn-a.akamaihd.net\/steamcommunity\/public\/images\/avatars\/fe\/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
            "steam_avatar_full": "https:\/\/steamcdn-a.akamaihd.net\/steamcommunity\/public\/images\/avatars\/fe\/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg"
        }
    }
}
```

## 3.7 UserDeRegisteredFromTournamentEvent

Submitted when a user de-registers from a tournament.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{USER_ID}

### Payload

```json
{
    "data": {
        "user_id": 1,
        "team_id": 1
    }
}
```

## 3.8 UserCheckedInToTournamentEvent

Submitted when a user checks in to a tournament.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "tournament_user": {
        "tournament_id": 1,
        "user_id": 1
    }
}
```

## 3.9 TeamUpdatedEvent

Submitted when a team gets updated by endpoint Teams.update.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "id": 1,
    "tournament_id": 1,
    "title": "Team America"
}
```

## 3.10 TeamDestroyedEvent

Submitted when a team gets destroyed by endpoint Teams.delete.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{USER_ATTACHED_TO_TEAM_ID}

### Payload

```json
{
    "id": 1,
    "tournament_id": 1
}
```

## 3.12 PlayerDetachedFromTeamEvent

Submitted when a user gets detached from a team by endpoint Teams.detach_player.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{USER_ID}

### Payload

```json
{
    "id": 1,
    "reserve": [
        {
            "id": 1,
            "username": "username",
            "elo": 999,
            "esportal_username": "Player",
            "is_streamer": false,
            "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
            "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
            "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            "checked_in": true,
            "order": 1
        }
    ],
    "teams": [
        {
            "id": 1,
            "title": "Team 1",
            "average_elo": 999,
            "users": [
                {
                    "id": 2,
                    "username": "username",
                    "elo": 999,
                    "esportal_username": "Player",
                    "is_streamer": false,
                    "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
                    "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
                    "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
                    "checked_in": true,
                    "order": 2
                }
            ]
        }
    ]
}
```

## 3.13 PlayersSwitchedEvent

Submitted when two players gets switched by endpoint Tournament.switch_players.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}
- App.Models.User.{FIRST_USER_ID}
- App.Models.User.{SECOND_USER_ID}

### Payload

```json
{
    "user_ids": [
        1,
        2
    ]
}
```

## 3.14 TeamsScrambledEvent

Submitted when teams are scrambled by endpoint Tournament.scramble_teams.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "reserve": [
        {
            "id": 1,
            "username": "username",
            "elo": 999,
            "esportal_username": "Player",
            "is_streamer": false,
            "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
            "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
            "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            "checked_in": true,
            "order": 1
        }
    ],
    "teams": [
        {
            "id": 1,
            "title": "Team 1",
            "average_elo": 999,
            "users": [
                {
                    "id": 2,
                    "username": "username",
                    "elo": 999,
                    "esportal_username": "Player",
                    "is_streamer": false,
                    "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
                    "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
                    "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
                    "checked_in": true,
                    "order": 2
                }
            ]
        }
    ]
}
```

## 3.14 PlayerMoved

Submitted when a player is moved to or from a team.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "reserve": [
        {
            "id": 1,
            "username": "username",
            "elo": 999,
            "esportal_username": "Player",
            "is_streamer": false,
            "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
            "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
            "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            "checked_in": true,
            "order": 1
        }
    ],
    "teams": [
        {
            "id": 1,
            "title": "Team 1",
            "average_elo": 999,
            "users": [
                {
                    "id": 2,
                    "username": "username",
                    "elo": 999,
                    "esportal_username": "Player",
                    "is_streamer": false,
                    "steam_avatar": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg",
                    "steam_avatar_medium": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_medium.jpg",
                    "steam_avatar_full": "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
                    "checked_in": true,
                    "order": 2
                }
            ]
        }
    ]
}
```

## 3.15 PlayerLocked

Submitted when a player is locked in a tournament.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "user_id": 1
}
```

## 3.16 PlayerUnlocked

Submitted when a player is unlocked in a tournament.

### Channels

- App.Models.Tournament.{TOURNAMENT_ID}

### Payload

```json
{
    "user_id": 1
}
```
