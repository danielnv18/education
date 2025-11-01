# Permission Matrix

Key:
- `Global` – action allowed across the entire application.
- `Course` – action limited to courses where the user is the owner (teacher) or assigned assistant.
- `View` – read-only visibility without modification.
- `Self` – limited to the user’s own account or submissions.
- `—` – not permitted.

## User & Role Management

| Capability                                               | Admin  | Content Manager | Teacher | Assistant | Student | Notes                                                                                         |
|----------------------------------------------------------|--------|-----------------|---------|-----------|---------|-----------------------------------------------------------------------------------------------|
| Provision users / send new invitations                   | Global | —               | —       | —         | —       | Admin-exclusive per clarified requirements.                                                   |
| Resend course invitations                                | Global | Global          | Course  | Course    | —       | Teachers/assistants limited to courses they belong to; content managers act across courses.   |
| Revoke invitations                                       | Global | Global          | Course  | Course    | —       | Course-scoped for teacher/assistant; global for admin/content manager.                        |
| Update existing user accounts                            | Global | —               | —       | —         | Self    | Students manage only their own profile settings.                                              |
| Send password reset emails                               | Global | —               | —       | —         | Self    | Students trigger their own reset; admins can initiate resets for any user.                    |
| Delete users                                             | Global | —               | —       | —         | —       | Admin responsibility only.                                                                    |
| Restore soft-deleted users                               | Global | —               | —       | —         | —       | Admin responsibility only.                                                                    |
| Manage global roles (`admin`, `content_manager`)         | Global | View            | —       | —         | —       | Content managers can review but not assign elevated roles.                                    |
| Assign course teachers/assistants                        | Global | Global          | Course  | —         | —       | Assistants cannot change course-level staffing; teachers manage their courses’ assignments.   |
| Manage student enrollments (invite, activate/deactivate) | Global | Global          | Course  | View      | —       | Assistants can monitor rosters but do not change enrollment status; teachers can.             |
| View user directory                                      | Global | Global          | View    | View      | Self    | Teachers/assistants see people tied to their courses; students only see themselves.           |

## Course & Content Authoring

| Capability                                 | Admin  | Content Manager | Teacher | Assistant | Student | Notes                                                                                   |
|--------------------------------------------|--------|-----------------|---------|-----------|---------|-----------------------------------------------------------------------------------------|
| Create / update / archive courses          | Global | Global          | Course  | —         | —       | Assistants focus on course content; teachers own course-level settings.                 |
| Publish / unpublish courses                | Global | Global          | Course  | —         | —       | Publishing is a course-level concern reserved for admins, content managers, and teachers. |
| Manage course metadata (banners, settings) | Global | Global          | Course  | —         | —       | Includes banner media, schedules, and status fields.                                    |
| Access inactive courses                    | Global | Global          | Course  | Course    | —       | Content managers review any course; assistants/teachers access courses they belong to.  |
| Create / reorder modules                   | Global | Global          | Course  | Course    | —       | From Module & Lesson Builder requirements.                                              |
| Create / edit lessons & attachments        | Global | Global          | Course  | Course    | —       | Includes TipTap authoring and media uploads.                                            |

## Assessments

| Capability                             | Admin  | Content Manager | Teacher | Assistant | Student | Notes                                                                             |
|----------------------------------------|--------|-----------------|---------|-----------|---------|-----------------------------------------------------------------------------------|
| Create / manage assignments            | Global | Global          | Course  | Course    | —       | Stated in TODO “assignments & submissions” section.                               |
| Grade assignments & manage feedback    | Global | Global          | Course  | Course    | —       | Teachers/assistants run submission review; admins/content managers can intervene. |
| Submit assignments                     | —      | —               | —       | —         | Self    | Learner submission flow.                                                          |
| Create / manage exams & question banks | Global | Global          | Course  | Course    | —       | Explicit in TODO “Ensure teachers, assistants, and content managers…”.            |
| Grade exams / manage attempts          | Global | Global          | Course  | Course    | —       | Includes autosave review and grading queues.                                      |
| Take exams                             | —      | —               | —       | —         | Self    | Students attempt exams they’re enrolled in.                                       |

## Attendance & Analytics

| Capability                                  | Admin  | Content Manager | Teacher | Assistant | Student | Notes                                                   |
|---------------------------------------------|--------|-----------------|---------|-----------|---------|---------------------------------------------------------|
| Create attendance sessions                  | Global | Global          | Course  | Course    | —       | From TODO “Attendance Tracking”.                        |
| Record attendance statuses                  | Global | Global          | Course  | Course    | —       |                                                         |
| View attendance analytics                   | Global | Global          | Course  | Course    | Self    | Students see only their own attendance timeline.       |
| Access assignment/exam analytics dashboards | Global | Global          | Course  | Course    | Self    | Students view personal progress only.                   |

## Platform Settings & Infrastructure

| Capability                                                 | Admin  | Content Manager | Teacher | Assistant | Student | Notes                                                                         |
|------------------------------------------------------------|--------|-----------------|---------|-----------|---------|-------------------------------------------------------------------------------|
| Manage global settings (timezones, grading scales, queues) | Global | Global          | —       | —         | —       | Architecture notes specify admins & content managers oversee global settings. |
| Configure notification channels / audit logging            | Global | Global          | —       | —         | —       | From SCHEMA “Notifications & Auditing Support”.                               |
| Trigger background jobs / queue management                 | Global | Global          | View    | View      | —       | Teachers/assistants monitor status but do not administer queues.              |
| Access dashboards & notifications center                   | Global | Global          | Course  | Course    | Self    | Per PAGES global experience; students receive learner dashboard only.         |

## Course Role Behaviors
- Course roles apply uniformly across every course instance; there are no per-course capability overrides.
- Students can browse published course content, submit assignments/exams, and interact with learning materials but cannot modify data.
- Teachers mirror assistant abilities and additionally manage course-level settings, publishing state, scheduling, and enrollment adjustments.
- Assistants focus exclusively on course content (modules, lessons, assignments, exams, attendance) and cannot alter course metadata or staffing.

## DTO & Type Synchronisation Strategy
- DTOs are implemented with `spatie/laravel-data` and transformed into TypeScript definitions via `spatie/typescript-transformer`, ensuring backend payloads stay aligned with Inertia React components.
