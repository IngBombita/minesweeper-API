# Minesweeper

### Solution to DevIGet Challenge

I thought that a minesweeper is always square, so I prepare the API to accept the size
of the board, and it makes an `size x size` minesweeper game. I decided to
let at that way because I'm running out of time.

You can't see the mines till you click one or win the game.

## Installation

### Prerequisites

- PHP 8.0 (with suitable extensions)
- Composer
- Redis server

### Step by step

- `git@github.com:IngBombita/minesweeper-API.git && cd minesweeper-API`
- `cp .env.example .env`
- Edit your redis host, port and pass on `.env`
- `composer install`
- `php artisan key:generate`
- `php artisan serve`

## API

Note: {host} is refering to the domain where the app is running, usually localhost

### Create a new game
```
POST {host}:8000/v1/games

BODY
{
    "size": Required, Integer, No less than 3. Description: Is the length of the board
    "mines": Required, Integer, No more than size * size. Description: The quantity of mines
}

EXAMPLE BODY
{
    "size": 4,
    "mines": 5
}

RESPONSES

200 OK:
{
    "mines": 5,
    "board": {
        "size": 4,
        "cells": [
            [
                {
                    "position": [
                        0,
                        0
                    ],
                    "clicked": false,
                    "flagged": false,
                    "value": null
                },
                {...}
            ]
        ]
    },
    "status": "created",
    "uuid": "bc8778d4-241f-4c60-bcdf-8c63ef9e073c"
}

400 BAD REQUEST:
{
    "errors": Array describing why is a bad request.
}
500 Internal server error:
{
    "error": String describing why the request failed.
}
```

### Get game stats
```
GET {host}:8000/v1/games/{id}

PATH PARAMS
{
    "id": Description: The identity of the game
}

EXAMPLE
localhost:8000/v1/games/a3b205cc-470f-4ff6-8ba7-696df49b7a09

RESPONSES

200 OK:
{
    "mines": 5,
    "board": {
        "size": 4,
        "cells": [
            [
                {
                    "position": [
                        0,
                        0
                    ],
                    "clicked": false,
                    "flagged": false,
                    "value": null
                },
                {...}
            ]
        ]
    },
    "status": "created",
    "uuid": "bc8778d4-241f-4c60-bcdf-8c63ef9e073c"
}

404 BAD REQUEST:
{
    "error": Game not found
}
500 Internal server error:
{
    "error": String describing why the request failed.
}
```

### List games
```
GET {host}:8000/v1/games

RESPONSES

200 OK:
[{
    "mines": 5,
    "board": {
        "size": 4,
        "cells": [
            [
                {
                    "position": [
                        0,
                        0
                    ],
                    "clicked": false,
                    "flagged": false,
                    "value": null
                },
                {...}
            ]
        ]
    },
    "status": "created",
    "uuid": "bc8778d4-241f-4c60-bcdf-8c63ef9e073c"
},
{...}
]

500 Internal server error:
{
    "error": String describing why the request failed.
}
```

### Update a cell of a game
```
PUT {host}:8000/v1/games/{id}/cell

BODY:
{
    "action":
        Required, Posible values: "click", "flag", "unflag"
        Descripton: The action to make to the cell
    "row": Required, Integer. Description: Number of row of the cell to update
    "column": Required, Integer. Description: Number of column of the cell to update
}

EXAMPLE BODY:
{
    "action": "click",
    "row": 1,
    "column": 2
}

RESPONSES

200 OK:
{
    "mines": 5,
    "board": {
        "size": 4,
        "cells": [
            [
                {
                    "position": [
                        0,
                        0
                    ],
                    "clicked": false,
                    "flagged": false,
                    "value": null
                },
                {...}
            ]
        ]
    },
    "status": "lost",
    "uuid": "bc8778d4-241f-4c60-bcdf-8c63ef9e073c",
    "startedAt": {
        "date": "2021-12-31 23:42:10.633820",
        "timezone_type": 3,
        "timezone": "UTC"
    },
    "endedAt": {
        "date": "2021-12-31 23:42:10.633839",
        "timezone_type": 3,
        "timezone": "UTC"
    }
}
409 Conflict:
{
    "error": String describing why the request could not be done
}

500 Internal server error:
{
    "error": String describing why the request failed.
}
```
