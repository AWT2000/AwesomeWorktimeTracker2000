# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0]

### Added
- Project-model (model + migration)
- Team-model (model + migration)
- WorktimeEntry-model (model + migration)
- Relationships
  - User can belong to many teams
  - User can belong to many projects (personal projects, teams' projects)
  - Project can belong to many users (personal projects, teams' projects)
  - Project can belong to many teams
  - Team can belong many users
  - Team belongs to supervisor
  - WorktimeEntry belongs to user
  - WorktimeEntry can belong to project
- Laravel/Passport authentication
- API for auth
  - Login: returns personal access token
- API for projects
  - Index: returns list of projects that user is attached to
- API for work time entries
  - Index: returns list of worktime entries that belongs to user (can be queried by started_at and ended_at attributes, default last 14 days)
  - Show: returns a worktime entry by id
  - Store: creates a new work time entry
- Validation for login
- Validation for work time entry index query
- Validation for creating work time entries
- Seeders for ACL, Superadmin and dummy data