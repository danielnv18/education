# LMS Schema & Model Plan

## Conventions & Shared Columns
- Rely on Laravel's default unsigned big integer auto-increment primary keys; reserve UUIDs only for external integrations that require them.
- Back every status field with a PHP backed enum; persist the enum value in the database using `string` columns (typically length 50) and enforce enum consistency via casts/accessors.
- Apply `softDeletes()` and `timestamps()` on all authorable resources; add `published_at` columns where publishing cadence is needed.
- Store datetimes in UTC with `->nullable()` only when business rules permit; add indexes to `publish_at`, `invite_expires_at`, and other query-heavy columns.
- Add `created_by_id` / `updated_by_id` foreign keys when auditing creator/editor is necessary; reference `users`.

## Core User Enhancements
1. **Migration:** `create_user_profiles_table`
   - Columns: `id`, `user_id` (unique, FK to `users`), `display_name`, `bio` (longText for Markdown), `avatar_url`, `timezone`, `locale`, `links` (json), timestamps.
   - Add indexes on `user_id` (unique) and `timezone` to assist scheduling queries.
2. **Model:** update `App\Models\User`
   - Add `profile()` one-to-one relationship; eager-load where appropriate for avatar/timezone access.
   - Maintain relationships for courses (`teachingCourses`, `assistingCourses`, `enrolledCourses`), `invitations`, `examAttempts`, `submissions`, `attendanceEntries`.
3. **Model:** create `App\Models\UserProfile`
   - Define inverse `user()` relationship, casts for `links` json payload, and accessors for derived display properties.

## Roles & Permissions (spatie/laravel-permission)
1. **Vendor migrations:** package already installed; publish and run defaults (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) once core domain tables are in place.
2. **Seeder alignment:** create seeders for base roles and permissions, treating `content_manager` as a global role alongside `admin`.
3. **Model updates:** ensure `User` uses `HasRoles`.

## Course Structure
1. **Courses**
   - **Migration:** `create_courses_table`
     - Columns: `id`, `slug` (unique), `title`, `description` (longText, stores Markdown), `banner_media_id` (nullable, references `media`), `owner_id`, `status` (string referencing `CourseStatus` enum such as `draft`, `published`, `archived`), `published_at`, `starts_at`, `ends_at`, `metadata` (json), `created_by_id`, `updated_by_id`.
     - Indexes: `unique slug`, `status`, `published_at`, `starts_at`, `ends_at`.
   - **Model:** `App\Models\Course`
     - Relationships: `owner`, `modules`, `lessons` (through modules), `assistants`, `teachers`, `students`, `invitations`, `assignments`, `exams`, `attendanceSessions`.
     - Cast `metadata` to array.
2. **Course Enrollments Pivot**
   - **Migration:** `create_course_user_table`
     - Columns: `course_id`, `user_id`, `role` (string referencing `CourseRole` enum: `teacher`, `student`, `assistant`), `assistant_scope` (json), `enrolled_at`, `invited_at`, `invitation_id` (nullable), `status` (string referencing `EnrollmentStatus` enum such as `pending`, `active`, `inactive`), timestamps.
     - Composite primary key on `(course_id, user_id)`, index `role`, `status`.
   - **Model:** `App\Models\CourseUser` (pivot extending `Pivot`)
     - Accessors for capabilities; casts for `assistant_scope`.
     - Content managers are granted access via global role assignments, not stored in this pivot.

## Modules & Lessons
1. **Modules**
   - **Migration:** `create_modules_table`
     - Columns: `id`, `course_id`, `title`, `description` (longText, Markdown), `order`, `type` (`content`, `assignment`, `exam`), `publish_at`, `unpublish_at`, `is_published` (computed via publish window), `metadata` (json), audit columns.
     - Indexes: `course_id + order`, `publish_at`.
   - **Model:** `App\Models\Module`
     - Relationships: `course`, `lessons`, `assignments`, `exams`.
     - Casts: `metadata`.
2. **Lessons**
   - **Migration:** `create_lessons_table`
     - Columns: `id`, `module_id`, `title`, `slug`, `summary`, `content` (longText, Markdown default), `content_type` (`markdown`, `video_embed`, `document_bundle`), `publish_at`, `unpublish_at`, `order`, `duration_minutes`, `metadata` (json), audit columns.
     - Indexes: `module_id + order`, `slug`, `publish_at`.
   - **Model:** `App\Models\Lesson`
     - Relationships: `module`, `course` (through module), `media`, `attachments`.
     - Casts: `metadata`.
3. **Lesson Media Attachments**
   - **Migration:** rely on `spatie/laravel-medialibrary` `media` table; create polymorphic usage guidelines.
   - **Model:** use `InteractsWithMedia` on `Lesson` and configure collections once base models are scaffolded.

## Invitations & Enrollment Workflow
1. **Invitations**
   - **Migration:** `create_invitations_table`
     - Columns: `id`, `course_id` (nullable for global invites), `email`, `inviter_id`, `invitee_id` (nullable), `role`, `token`, `status` (string referencing `InvitationStatus` enum such as `pending`, `accepted`, `declined`, `revoked`), `sent_at`, `responded_at`, `expires_at`, `metadata`, timestamps.
     - Indexes: `email`, `token` unique, `status`.
   - **Model:** `App\Models\Invitation`
     - Relationships: `course`, `inviter`, `invitee`.

## Assignments & Submissions
1. **Assignments**
   - **Migration:** `create_assignments_table`
     - Columns: `id`, `module_id`, `title`, `instructions` (longText, Markdown), `type` (`essay`, `upload`, `quiz`, `project`), `points_possible`, `open_at`, `due_at`, `close_at`, `publish_at`, `allow_late_submissions` (bool), `metadata`, `created_by_id`, `updated_by_id`.
     - Indexes: `module_id`, `publish_at`, `open_at`, `due_at`, `close_at`.
   - **Model:** `App\Models\Assignment`
     - Relationships: `module`, `course`, `submissions`, `media`, `attachments`.
     - Casts: `metadata`.
2. **Assignment Extensions**
   - **Migration:** `create_assignment_extensions_table`
     - Columns: `id`, `assignment_id`, `user_id`, `extended_due_at`, `granted_by_id`, `reason`, timestamps.
     - Unique index `assignment_id + user_id`.
   - **Model:** `App\Models\AssignmentExtension`.
3. **Submissions**
   - **Migration:** `create_submissions_table`
     - Columns: `id`, `assignment_id`, `user_id`, `status` (string referencing `SubmissionStatus` enum such as `draft`, `submitted`, `graded`, `returned`), `submitted_at`, `graded_at`, `grade`, `feedback` (longText, Markdown), `metadata`, `created_by_id`, `updated_by_id`.
     - Indexes: `assignment_id`, `user_id`, `status`.
   - **Model:** `App\Models\Submission`
     - Relationships: `assignment`, `student`, `grader`, `media`.
4. **Submission Activities**
   - **Migration:** `create_submission_events_table`
     - Columns: `id`, `submission_id`, `actor_id`, `type` (`autosave`, `comment`, `status_change`), `payload` (json), timestamps.
   - **Model:** `App\Models\SubmissionEvent`.

## Exams & Question Banks
1. **Question Banks**
   - **Migration:** `create_question_banks_table`
     - Columns: `id`, `course_id`, `title`, `description` (longText, Markdown), `created_by_id`, `updated_by_id`, timestamps.
     - Indexes: `course_id`.
   - **Model:** `App\Models\QuestionBank`.
2. **Questions**
   - **Migration:** `create_questions_table`
     - Columns: `id`, `question_bank_id`, `type` (`single_choice`, `multiple_choice`, `rich_text`), `prompt` (longText, Markdown), `metadata`, `points`, `created_by_id`, `updated_by_id`, timestamps.
     - Indexes: `question_bank_id`, `type`.
   - **Model:** `App\Models\Question`.
3. **Question Options**
   - **Migration:** `create_question_options_table`
     - Columns: `id`, `question_id`, `label`, `value`, `is_correct`, `order`, timestamps.
     - Indexes: `question_id`, `order`.
   - **Model:** `App\Models\QuestionOption`.
4. **Exams**
   - **Migration:** `create_exams_table`
     - Columns: `id`, `module_id`, `title`, `instructions` (longText, Markdown), `time_limit_minutes`, `attempt_limit`, `availability_starts_at`, `availability_ends_at`, `publish_at`, `metadata`, `created_by_id`, `updated_by_id`.
     - Indexes: `module_id`, `publish_at`, `availability_starts_at`.
   - **Model:** `App\Models\Exam`.
5. **Exam Sections**
   - **Migration:** `create_exam_sections_table`
     - Columns: `id`, `exam_id`, `title`, `order`, `question_bank_id` (nullable), `question_selection_mode` (`all`, `random`), `question_count`, `metadata`.
     - Indexes: `exam_id`, `question_bank_id`.
   - **Model:** `App\Models\ExamSection`.
6. **Exam Section Questions**
   - **Migration:** `create_exam_section_question_table`
     - Columns: `exam_section_id`, `question_id`, `order`, `points_override` (nullable).
     - Composite primary key `(exam_section_id, question_id)`.
   - **Pivot Model:** `ExamSectionQuestion`.
7. **Exam Attempts**
   - **Migration:** `create_exam_attempts_table`
     - Columns: `id`, `exam_id`, `user_id`, `status` (string referencing `AttemptStatus` enum such as `in_progress`, `submitted`, `graded`, `expired`), `started_at`, `submitted_at`, `graded_at`, `score`, `metadata`.
     - Indexes: `exam_id`, `user_id`, `status`.
   - **Model:** `App\Models\ExamAttempt`.
8. **Attempt Responses**
   - **Migration:** `create_attempt_responses_table`
     - Columns: `id`, `exam_attempt_id`, `question_id`, `response` (json), `is_correct` (nullable until graded), `autosaved_at`, timestamps.
     - Indexes: `exam_attempt_id`, `question_id`.
   - **Model:** `App\Models\AttemptResponse`.

## Attendance Tracking
1. **Attendance Sessions**
   - **Migration:** `create_attendance_sessions_table`
     - Columns: `id`, `course_id`, `title`, `held_at`, `created_by_id`, `notes` (longText, Markdown), timestamps.
     - Indexes: `course_id`, `held_at`.
   - **Model:** `App\Models\AttendanceSession`.
2. **Attendance Entries**
   - **Migration:** `create_attendance_entries_table`
     - Columns: `id`, `attendance_session_id`, `user_id`, `status` (string referencing `AttendanceStatus` enum such as `present`, `late`, `absent`), `recorded_by_id`, `justification` (longText, Markdown), `recorded_at`, timestamps.
     - Unique index: `(attendance_session_id, user_id)`.
   - **Model:** `App\Models\AttendanceEntry`.

## Media & Files (spatie/laravel-medialibrary)
- Publish media library migration (`create_media_table`) and keep default incrementing primary key configuration for consistency once model scaffolding begins.
- Configure model-specific collections:
  - `Course`: `banner` collection with 16:9 validation, single file.
  - `Lesson`: `resources` collection allowing documents (PDF, DOCX, PPTX, XLSX, TXT), limit 10 MB, ordered by `media.order_column`.
  - `Assignment`: `attachments` collection (same rules as lessons).
  - `Submission`: `artifacts` collection for student uploads, 25 MB max.
  - `Question`: optional `illustrations`.

## Notifications & Auditing Support
1. **Notification Logs**
   - **Migration:** `create_notification_logs_table`
     - Columns: `id`, `type`, `notifiable_type`, `notifiable_id`, `channel`, `payload`, `sent_at`, timestamps.
   - **Model:** `App\Models\NotificationLog`.
2. **Audit Trail** (optional for MVP) – if included, `create_audit_logs_table` storing `event`, `subject_type`, `subject_id`, `causer_id`, `payload`.

## DTO Synchronization Helpers
- Plan to create DTOs parallel to models (`app/Data/...`), aligning with schema.
- Configure `spatie/typescript-transformer` to scan DTO namespace; add custom Artisan command to regenerate on schema changes.

## Frontend Implementation Notes
- Default to Shadcn UI components for Inertia React pages, pairing them with TanStack Table, dialog, and form primitives to keep UX consistent.
- Compose layouts and design tokens with Tailwind CSS v4 utilities, using new color, spacing, and typography features where they simplify styling.
- Ensure DTO-driven TypeScript definitions align with Shadcn component props and validation states, keeping error/pending states consistent across forms.

## Next Steps
1. Generate migrations and models with `php artisan make:model -m` where applicable, ensuring actions/form requests reference generated schema.
2. Update factories for new models to facilitate seeding and tests.
3. Run `vendor/bin/pint --dirty` after model creation, and create Pest feature tests covering new relationships once implemented.
