# LMS Schema & Model Plan

## Conventions & Shared Columns
- Rely on Laravel's default unsigned big integer auto-increment primary keys; reserve UUIDs only for external integrations that require them.
- Back every status field with a PHP backed enum; persist the enum value in the database using `string` columns (typically length 50) and enforce enum consistency via casts/accessors.
- Apply `softDeletes()` (persisting a nullable `deleted_at` column) and `timestamps()` on every model described below, including `users`, so records can be restored without losing historical context; add `published_at` columns where publishing cadence is needed.
- Store datetimes in UTC with `->nullable()` only when business rules permit; add indexes to `publish_at`, `invite_expires_at`, and other query-heavy columns.
- Add `created_by_id` / `updated_by_id` foreign keys when auditing creator/editor is necessary; reference `users`.

## Core User Enhancements
- Ensure the `users` table includes a nullable `deleted_at` column and that `App\Models\User` uses the `SoftDeletes` trait so accounts can be restored after deactivation.
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
All course-centric tables (`courses`, modules, enrollment pivots) store a nullable `deleted_at` column so the related models can leverage Laravel soft deletes for archival and restoration workflows.
1. **Courses**
   - **Migration:** `create_courses_table`
     - Columns: `id`, `slug` (unique), `title`, `description` (longText, stores Markdown), `banner_media_id` (nullable, references `media`), `owner_id` (teacher of record), `status` (string referencing `CourseStatus` enum such as `draft`, `published`, `archived`), `published_at`, `starts_at`, `ends_at`, `metadata` (json for course-level preferences like featured flags or highlight colors), `created_by_id`, `updated_by_id`.
     - Indexes: `unique slug`, `status`, `published_at`, `starts_at`, `ends_at`.
   - **Model:** `App\Models\Course`
     - Relationships: `owner`, `modules`, `lessons` (through modules), `assistants`, `teachers`, `students`, `invitations`, `assignments`, `exams`, `attendanceSessions`.
     - Cast `metadata` to array.
2. **Course Enrollments Pivot**
   - **Migration:** `create_course_user_table`
     - Columns: `course_id`, `user_id`, `role` (string referencing `CourseRole` enum: `teacher`, `student`, `assistant`), `enrolled_at`, `invited_at`, `invitation_id` (nullable), `status` (string referencing `EnrollmentStatus` enum such as `pending`, `active`, `inactive`), timestamps.
     - Composite primary key on `(course_id, user_id)`, index `role`, `status`.
   - **Model:** `App\Models\CourseUser` (pivot extending `Pivot`)
     - Assistants inherit a consistent capability set defined by roles and gain course-level access only to the courses they’re attached to; no per-course capability column is required.
     - Enforce a single `teacher` row per course (unique index on `course_id` + `role` for `teacher`) while permitting many assistants.
     - Content managers are granted access via global role assignments, not stored in this pivot.

## Modules & Lessons
Modules, lessons, and their related attachments use `deleted_at` to support soft deletion without permanently losing authored content.
1. **Modules**
   - **Migration:** `create_modules_table`
     - Columns: `id`, `course_id`, `title`, `description` (longText, Markdown), `order`, `type` (`content`, `assignment`, `exam`), `publish_at`, `unpublish_at`, `is_published` (computed via publish window), `metadata` (json storing scheduling or visibility toggles), audit columns.
     - Indexes: `course_id + order`, `publish_at`.
   - **Model:** `App\Models\Module`
     - Relationships: `course`, `lessons`, `assignments`, `exams`.
     - Casts: `metadata`.
2. **Lessons**
   - **Migration:** `create_lessons_table`
     - Columns: `id`, `module_id`, `title`, `slug`, `summary`, `content` (longText, Markdown default), `content_type` (`markdown`, `video_embed`, `document_bundle`), `publish_at`, `unpublish_at`, `order`, `duration_minutes`, `metadata` (json with authoring preferences like default tabs or transcript language), audit columns.
     - Indexes: `module_id + order`, `slug`, `publish_at`.
   - **Model:** `App\Models\Lesson`
     - Relationships: `module`, `course` (through module), `media`, `attachments`.
     - Casts: `metadata`.
3. **Lesson Media Attachments**
   - **Migration:** rely on `spatie/laravel-medialibrary` `media` table; create polymorphic usage guidelines.
   - **Model:** use `InteractsWithMedia` on `Lesson` and configure collections once base models are scaffolded.

## Invitations & Enrollment Workflow
Invitations and enrollment records rely on soft deletes to keep historical audit trails when an invite is withdrawn or a pivot membership is retired.
1. **Invitations**
   - **Migration:** `create_invitations_table`
     - Columns: `id`, `course_id` (nullable for global invites), `email`, `inviter_id`, `invitee_id` (nullable), `role`, `token`, `status` (string referencing `InvitationStatus` enum such as `pending`, `accepted`, `declined`, `revoked`), `sent_at`, `responded_at`, `expires_at`, `metadata` (json for delivery metrics like resend counts), timestamps.
     - Indexes: `email`, `token` unique, `status`.
   - **Model:** `App\Models\Invitation`
     - Relationships: `course`, `inviter`, `invitee`.

## Assignments & Submissions
Assignment, submission, and extension tables expose `deleted_at` to allow reversible removals during content cleanup or grade disputes.
1. **Assignments**
   - **Migration:** `create_assignments_table`
     - Columns: `id`, `module_id`, `title`, `instructions` (longText, Markdown), `type` (`essay`, `upload`, `quiz`, `project`), `points_possible`, `open_at`, `due_at`, `close_at`, `publish_at`, `allow_late_submissions` (bool), `metadata` (json for rubric references, submission caps, grading settings), `created_by_id`, `updated_by_id`.
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
     - Columns: `id`, `assignment_id`, `user_id`, `status` (string referencing `SubmissionStatus` enum such as `draft`, `submitted`, `graded`, `returned`), `submitted_at`, `graded_at`, `grade`, `feedback` (longText, Markdown), `metadata` (json capturing override flags, late approvals, or artefact hints), `created_by_id`, `updated_by_id`.
     - Indexes: `assignment_id`, `user_id`, `status`.
   - **Model:** `App\Models\Submission`
     - Relationships: `assignment`, `student`, `grader`, `media`.
4. **Submission Activities**
   - **Migration:** `create_submission_events_table`
     - Columns: `id`, `submission_id`, `actor_id`, `type` (`autosave`, `comment`, `status_change`), `payload` (json containing event-specific data), timestamps.
   - **Model:** `App\Models\SubmissionEvent`.

## Exams & Question Banks
Question banks, questions, options, exams, sections, and attempts all include `deleted_at` columns and rely on `SoftDeletes` in their models for reversible archival.
1. **Question Banks**
   - **Migration:** `create_question_banks_table`
     - Columns: `id`, `course_id`, `title`, `description` (longText, Markdown), `created_by_id`, `updated_by_id`, timestamps.
     - Indexes: `course_id`.
   - **Model:** `App\Models\QuestionBank`.
2. **Questions**
   - **Migration:** `create_questions_table`
     - Columns: `id`, `question_bank_id`, `type` (`single_choice`, `multiple_choice`, `rich_text`), `prompt` (longText, Markdown), `metadata` (json for difficulty, tags, optional hints), `points`, `created_by_id`, `updated_by_id`, timestamps.
     - Indexes: `question_bank_id`, `type`.
   - **Model:** `App\Models\Question`.
3. **Question Options**
   - **Migration:** `create_question_options_table`
     - Columns: `id`, `question_id`, `label`, `value`, `is_correct`, `order`, timestamps.
     - Indexes: `question_id`, `order`.
   - **Model:** `App\Models\QuestionOption`.
4. **Exams**
   - **Migration:** `create_exams_table`
     - Columns: `id`, `module_id`, `title`, `instructions` (longText, Markdown), `time_limit_minutes`, `attempt_limit`, `availability_starts_at`, `availability_ends_at`, `publish_at`, `metadata` (json for grading policy, proctoring requirements, or shuffle settings), `created_by_id`, `updated_by_id`.
     - Indexes: `module_id`, `publish_at`, `availability_starts_at`.
   - **Model:** `App\Models\Exam`.
5. **Exam Sections**
   - **Migration:** `create_exam_sections_table`
     - Columns: `id`, `exam_id`, `title`, `order`, `question_bank_id` (nullable), `question_selection_mode` (`all`, `random`), `question_count`, `metadata` (json detailing section-level timing or shuffle rules).
     - Indexes: `exam_id`, `question_bank_id`.
   - **Model:** `App\Models\ExamSection`.
6. **Exam Section Questions**
   - **Migration:** `create_exam_section_question_table`
     - Columns: `exam_section_id`, `question_id`, `order`, `points_override` (nullable).
     - Composite primary key `(exam_section_id, question_id)`.
   - **Pivot Model:** `ExamSectionQuestion`.
7. **Exam Attempts**
   - **Migration:** `create_exam_attempts_table`
     - Columns: `id`, `exam_id`, `user_id`, `status` (string referencing `AttemptStatus` enum such as `in_progress`, `submitted`, `graded`, `expired`), `started_at`, `submitted_at`, `graded_at`, `score`, `metadata` (json for device info, integrity flags, or late offsets).
     - Indexes: `exam_id`, `user_id`, `status`.
   - **Model:** `App\Models\ExamAttempt`.
8. **Attempt Responses**
   - **Migration:** `create_attempt_responses_table`
     - Columns: `id`, `exam_attempt_id`, `question_id`, `response` (json storing selected option IDs or rich text content), `is_correct` (nullable until graded), `autosaved_at`, timestamps.
     - Indexes: `exam_attempt_id`, `question_id`.
   - **Model:** `App\Models\AttemptResponse`.
     - Autosave expectations: immediately persist when a select-style answer changes; delay writes for rich text questions until 30 seconds after the user stops typing (debounced per keystroke).

## Attendance Tracking
Attendance sessions and entries record `deleted_at` to allow instructors to correct mistakes without purging audit data.
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
   - Scopes should surface per-student aggregates (percent present, counts per status) to power roster summaries and learner history views without requiring bespoke SQL.

## Media & Files (spatie/laravel-medialibrary)
Medialibrary models already support soft deletes; ensure custom collections added to domain models respect `SoftDeletes` when querying related media.
- Publish media library migration (`create_media_table`) and keep default incrementing primary key configuration for consistency once model scaffolding begins.
- All collections use the default filesystem disk unless future requirements dictate overrides.
- Configure model-specific collections:
  - `Course`: `banner` collection with 16:9 validation, single file.
  - `Lesson`: `resources` collection allowing documents (PDF, DOCX, PPTX, XLSX, TXT), limit 10 MB, ordered by `media.order_column`.
  - `Assignment`: `attachments` collection (same rules as lessons).
  - `Submission`: `artifacts` collection for student uploads, 25 MB max.
  - `Question`: optional `illustrations`.
- In local/dev, persist media to the default disk; production deployments should swap to S3 (or compatible object storage) via configuration without code changes.
- Generate 4:3 and 16:9 thumbnail conversions via queued jobs to avoid blocking user requests.

## Notifications & Auditing Support
Notification and audit log tables keep `deleted_at` columns so records can be hidden when necessary without compromising future forensic needs.
1. **Notification Logs**
   - **Migration:** `create_notification_logs_table`
     - Columns: `id`, `type`, `notifiable_type`, `notifiable_id`, `channel`, `payload`, `sent_at`, timestamps.
   - **Model:** `App\Models\NotificationLog`.
2. **Audit Trail** (optional for MVP) – if included, `create_audit_logs_table` storing `event`, `subject_type`, `subject_id`, `causer_id`, `payload`.

## DTO Synchronization Helpers
- Plan to create DTOs parallel to models (`app/Data/...`), aligning with schema.
- Configure `spatie/typescript-transformer` to scan DTO namespace; add custom Artisan command to regenerate on schema changes.

## JSON Field Reference
- `UserProfile.links`: `{ label: string, url: string }[]`.
- `Course.metadata`: `{ featured?: bool, highlightColor?: string|null, defaultLayout?: string|null }`.
- `Module.metadata`: `{ releaseStrategy?: 'immediate'|'scheduled', visibilityNote?: string|null }`.
- `Lesson.metadata`: `{ defaultTab?: 'content'|'attachments', transcriptLanguage?: string|null }`.
- `Invitation.metadata`: `{ resendCount?: int, lastSentAt?: string|null }`.
- `Assignment.metadata`: `{ rubricId?: int|null, maxFiles?: int|null, submissionLimit?: int|null }`.
- `Submission.metadata`: `{ lateApproved?: bool, extensionId?: int|null, requiresManualReview?: bool }`.
- `SubmissionEvent.payload`: keyed by event type—for `comment` `{ body: string }`, for `status_change` `{ from: string, to: string }`, for `autosave` `{ fields: array }`.
- `Question.metadata`: `{ difficulty?: 'easy'|'medium'|'hard', tags?: string[], hint?: string|null }`.
- `Exam.metadata`: `{ gradingPolicy?: 'auto'|'manual', proctoringRequired?: bool, shuffleSections?: bool }`.
- `ExamSection.metadata`: `{ shuffleQuestions?: bool, timeLimitMinutes?: int|null }`.
- `ExamAttempt.metadata`: `{ ipAddress?: string|null, integrityFlags?: string[] }`.
- `AttemptResponse.response`: for select questions `{ selectedOptionIds: int[] }`; for rich text `{ content: string, draft?: bool }`.

## Next Steps
1. Generate migrations and models with `php artisan make:model -m` where applicable, ensuring actions/form requests reference generated schema.
2. Update factories for new models to facilitate seeding and tests.
3. Run `vendor/bin/pint --dirty` after model creation, and create Pest feature tests covering new relationships once implemented.
