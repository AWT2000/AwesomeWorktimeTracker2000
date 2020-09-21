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
        "created_at": "2020-01-01T00:00:00",
        "updated_at": "2020-01-01T00:00:00"
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

Returns array of work time entries:
```
{
    "worktime_entries": [
        {
            "id": 13,
            "project_id": 5,
            "started_at": "2020-09-14T11:37:25",
            "ended_at": "2020-09-14T12:59:03",
            "created_at": "2020-09-14T17:27:49",
            "updated_at": "2020-09-14T17:27:49"
        },
        {
            "id": 14,
            "project_id": 5,
            "started_at": "2020-09-13T11:29:00",
            "ended_at": "2020-09-13T16:32:19",
            "created_at": "2020-09-14T17:27:49",
            "updated_at": "2020-09-14T17:27:49"
        }
    ]
}
```

### POST /

Parameters:
```
{
    "project_id": 5, [optional]
    "started_at": "2020-01-01T16:00:00", [required]
    "ended_at": "2020-01-01T17:00:00" [optional]
}
```
Returns created work time entry:
```
{
    "id": 55,
    "project_id": 5,
    "started_at": "2020-01-01T16:00:00",
    "ended_at": "2020-01-01T17:00:00",
    "created_at": "2020-09-21T13:41:20",
    "updated_at": "2020-09-21T13:41:20"
}
```

### GET /{id}

Returns:
```
{
    "id": 13,
    "project_id": 5,
    "started_at": "2020-09-14T11:37:25",
    "ended_at": "2020-09-14T12:59:03",
    "created_at": "2020-09-14T17:27:49",
    "updated_at": "2020-09-14T17:27:49"
}
```
OR
```
{
    "message": "Not Found."
}
```

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
        "created_at": "2020-09-14T17:27:49",
        "updated_at": "2020-09-14T17:27:49",
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
        "created_at": "2020-09-14T17:27:49",
        "updated_at": "2020-09-14T17:27:49",
        "is_personal": false
    }
]
```
