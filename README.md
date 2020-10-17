# AwesomeWorktimeTracker2000

Laravel project for work time tracker app

## API documentation:

## /api/v1/auth

#### POST /login

Required parameters:
```
{
    "email": "user@example.net",
    "password": "password"
}
```
Returns:
```
{
    "user": {
        "id": 8,
        "name": "Miss Katlyn Spinka",
        "email": "user@example.net",
        "created_at": "2020-09-22T14:54:21+00:00",
        "updated_at": "2020-09-22T14:54:21+00:00"
    },
    "access_token":"access.token"
}
```
OR
```
{
    "message": "Invalid credentials."
}
```

## /api/v1/worktime-entries

Every end point returns:
```
status: 401
body:
{
    "message": "Unauthenticated."
}
```
if authentication header is not specified OR invalid token is passed

### GET /

Optional query parameters: started_at, ended_at
e.g. `?started_at=2020-01-01&ended_at=2020-01-02`
validation rule: must be in 'Y-m-d' format
defaults: started_at now, ended_at 14 days ago

Returns work time entries paginated (page size 30):
```
{
    "data": [
        {
            "id": 21,
            "project_id": 2,
            "started_at": "2020-09-28T16:32:30+00:00",
            "ended_at": "2020-09-28T18:05:04+00:00",
            "collides_with_other_entries": false,
            "created_at": "2020-09-22T14:54:21+00:00",
            "updated_at": "2020-09-22T14:54:21+00:00"
        },
        {
            "id": 22,
            "project_id": 2,
            "started_at": "2020-09-29T18:07:04+00:00",
            "ended_at": "2020-09-29T20:14:51+00:00",
            "collides_with_other_entries": false,
            "created_at": "2020-09-22T14:54:21+00:00",
            "updated_at": "2020-09-22T14:54:21+00:00"
        },
        {
            "id": 23,
            "project_id": 2,
            "started_at": "2020-09-30T16:06:02+00:00",
            "ended_at": "2020-09-30T22:28:43+00:00",
            "collides_with_other_entries": false,
            "created_at": "2020-09-22T14:54:21+00:00",
            "updated_at": "2020-09-22T14:54:21+00:00"
        },
        ...
    ],
    "links": {
        "first": "http://awesomeworktimetracker.test/api/v1/worktime-entries?page=1",
        "last": "http://awesomeworktimetracker.test/api/v1/worktime-entries?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "Previous",
                "active": false
            },
            {
                "url": "http://awesomeworktimetracker.test/api/v1/worktime-entries?page=1",
                "label": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next",
                "active": false
            }
        ],
        "path": "http://awesomeworktimetracker.test/api/v1/worktime-entries",
        "per_page": 30,
        "to": 12,
        "total": 12
    }
}
```

### POST /

Body:
```
{
    "project_id": 5, [optional]
    "started_at": "2020-10-05T21:23:25+00:00", [required]
    "ended_at": "2020-10-05T21:28:46+00:00" [optional]
}
```
Returns created work time entry:
```
{
    "id": 55,
    "project_id": 5,
    "started_at": "2020-10-05T21:23:25+00:00",
    "ended_at": "2020-10-05T21:28:46+00:00",
    "collides_with_other_entries": false,
    "created_at": "2020-09-22T14:54:21+00:00",
    "updated_at": "2020-09-22T14:54:21+00:00"
}
```

### GET /{id}

Returns:
```
{
    "id": 13,
    "project_id": 5,
    "started_at": "2020-10-05T21:23:25+00:00",
    "ended_at": "2020-10-05T21:28:46+00:00",
    "collides_with_other_entries": false,
    "created_at": "2020-09-22T14:54:21+00:00",
    "updated_at": "2020-09-22T14:54:21+00:00"
}
```
OR
```
{
    "message": "Not Found."
}
```
### PUT /{id}
Body:
```
{
    "project_id": 5, [optional]
    "started_at": "2020-10-05T21:23:25+00:00", [required]
    "ended_at": "2020-10-05T21:28:46+00:00" [optional]
}
```
Returns updated work time entry:
```
{
    "id": 55,
    "project_id": 5,
    "started_at": "2020-10-05T21:23:25+00:00",
    "ended_at": "2020-10-05T21:28:46+00:00",
    "collides_with_other_entries": false,
    "created_at": "2020-09-22T14:54:21+00:00",
    "updated_at": "2020-09-22T14:54:21+00:00"
}
```
### DELETE /{id}
Returns:
200 if ok and 404 if not found

## /api/v1/projects

### GET /
Returns array of projects:
```
[
    {
        "id": 7,
        "name": "aliquid quia -projekti",
        "founder": {
            "name": "Miss Katlyn Spinka",
            "email": "pstehr@example.net"
        },
        "project_manager": {
            "name": "Miss Katlyn Spinka",
            "email": "pstehr@example.net"
        },
        "created_at": "2020-09-22T14:54:21+00:00",
        "updated_at": "2020-09-22T14:54:21+00:00"
        "is_personal": true
    },
    {
        "id": 5,
        "name": "ea in -projekti",
        "founder": {
            "name": "Miss Verlie Kozey",
            "email": "mjohnston@example.org"
        },
        "project_manager": {
            "name": "Miss Verlie Kozey",
            "email": "mjohnston@example.org"
        },
        "created_at": "2020-09-22T14:54:21+00:00",
        "updated_at": "2020-09-22T14:54:21+00:00"
        "is_personal": false
    }
]
```
