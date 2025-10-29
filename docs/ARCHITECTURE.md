# Architecture Overview

## Domain Roles & Relationships
- **Teachers:** Each course has exactly one teacher-of-record. A teacher can own many courses, and teacher assignments are stored via the `course_user` pivot with the `teacher` role (enforced as one row per course). The `courses.owner_id` column points to the same teacher to simplify eager loading.
- **Assistants:** Assistants are attached to courses through the same pivot, flagged with the `assistant` role. They inherit a consistent capability set (content, grading, attendance) and gain access to every course where they have a pivot row. There is no per-course capability override.
- **Admins & Content Managers:** Global roles granted through `spatie/laravel-permission`. They can attach/detach teachers and assistants, manage invitations, and oversee global settings.
- **Students:** Learners join courses via the pivot using the `student` role. Enrollment status determines visibility of course content.

## Enumerations
| Enum | Allowed Values | Purpose |
| --- | --- | --- |
| `CourseStatus` | `draft`, `published`, `archived` | Controls learner visibility and publishing cadence. |
| `EnrollmentStatus` | `pending`, `active`, `inactive` | Tracks invitation lifecycle and course access for students/assistants. |
| `InvitationStatus` | `pending`, `accepted`, `declined`, `revoked` | Governs invitation workflows and resend capability. |
| `ModuleType` | `content`, `assignment`, `exam` | Drives tab visibility and feature toggles inside the course shell. |
| `LessonContentType` | `markdown`, `video_embed`, `document_bundle` | Selects editor presets and rendering pipelines. |
| `AssignmentType` | `essay`, `upload`, `quiz`, `project` | Configures grading options, submission form inputs, and validation. |
| `SubmissionStatus` | `draft`, `submitted`, `graded`, `returned` | Controls student access to editing, grading views, and analytics. |
| `QuestionType` | `single_choice`, `multiple_choice`, `rich_text` | Determines available authoring fields and grading mode. |
| `AttemptStatus` | `in_progress`, `submitted`, `graded`, `expired` | Indicates learner progress through exam attempts. |
| `AttendanceStatus` | `present`, `late`, `absent` | Drives attendance analytics and badge colors. |

All enums are implemented as PHP backed enums and persisted as strings (≤50 chars) to keep flexibility across services.

## Core Workflows
- **Course Publishing:** Courses remain in `draft` until explicitly published. Publishing expects at least one module and a teacher assignment. Archiving hides the course from learners but keeps admin visibility.
- **Assistant Assignment:** Admins or content managers attach assistants via the course People tab. Once added, assistants can manage modules, lessons, assignments, exams, and attendance for that course without further configuration.
- **Invitation Flow:** Invitations reference optional courses and roles. Pending invitations can be resent; acceptance activates the enrollment pivot and flips `EnrollmentStatus` to `active`. Declines or revocations mark the pivot `inactive`.
- **Lesson & Assignment Publishing:** Modules/lessons respect `publish_at`/`unpublish_at`. Assignments honor `open_at`, `due_at`, and `close_at`, with overrides via assignment extensions.
- **Exam Attempt Autosave:** Select questions persist instantly whenever answers change. Rich-text questions debounce saves to 30 seconds after the user stops typing (reset per keystroke). Autosave timestamps feed analytics and recovery flows.
- **Submission Review:** Graders operate from the submission detail view. After grading, status moves to `graded`; returning work flips to `returned`. Concurrency is handled via manual reloads or broadcast notifications (future enhancement).
- **Media Processing:** Files upload through Inertia forms. Locally, media is stored on the default disk; production swaps to S3-compatible storage via configuration. Thumbnail conversions (4:3 and 16:9) run asynchronously on the queue.

## Services & Supporting Layers
- **Action Classes (`app/Actions`)** encapsulate domain behavior (course creation, enrollment, grading) and provide single `handle()` entry points so controllers, jobs, and console commands remain thin.
- **Queued Jobs** handle heavy operations: media conversions, bulk notifications, and potential future grading batches.
- **DTOs (`app/Data`)** define contract-safe payloads for API/Inertia props. Paired with `spatie/typescript-transformer` to keep TypeScript definitions in sync.
- **Policies & Gates** leverage `spatie/laravel-permission` roles plus pivot data to gate course content, ensuring assistants only see assigned courses.
- **Notifications & Auditing** (future): `NotificationLog` table records outbound notifications; optional audit logs can track instructor edits if the feature is enabled.

## Frontend Architecture Notes
- **Inertia Patterns:** Use `<Form>` or `useForm` for all submissions; rely on shared props for auth context, flash messages, and notification counts. Apply `router.reload({ only: [...] })` to avoid full refreshes. Prefetch frequently visited routes (`prefetch cacheFor`) to keep navigation snappy.
- **Autosave & Polling:** Limit polling to timers and grading queues. Exam autosave uses debounced requests instead of continuous polling; submissions and attendance rely on manual refresh or future events.
- **Deferred Props:** Heavy data (lesson attachments, analytics) should use Inertia deferred props plus skeleton loaders to keep first paint quick.
- **File Uploads:** Let Inertia convert forms to `FormData`; monitor upload progress via `router.on('progress')`.

## Backend Architecture Notes
- **Relationships:** Use Eloquent pivot models (`CourseUser`) with scopes (`teachers()`, `assistants()`, `students()`) to keep queries expressive. Enforce single-teacher-per-course via database unique constraint (`unique(course_id, role)` for `teacher`) or validation in actions.
- **Enum Casting:** Models expose enum casts via the `casts()` method to guarantee consistent serialization/deserialization.
- **Validation:** Dedicated Form Request classes should reference enum rules and validate scheduling windows (e.g., `publish_at <= unpublish_at`).
- **Transactions:** Multi-model workflows (course creation with modules, enrollment with invitations) should run inside database transactions within their respective action classes.
- **Configuration:** Media disks, queue connections, and future settings should rely on environment configuration, allowing dev (local disk) and prod (S3) parity without code changes.

