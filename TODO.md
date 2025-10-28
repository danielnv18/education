# Learning Management System (LMS) MVP TODO

## Domain & Data Modelling
- [ ] Define Eloquent models for `User`, `Course`, `Module`, `Lesson`, and enrollment pivots following existing conventions.
- [ ] Create a `user_profiles` table (1:1 with `users`) for extended profile data including bio, avatar, locale, and timezone.
- [ ] Model relationships: users ↔ courses (teachers, assistants, students), courses → modules (ordered), modules → lessons (ordered).
- [ ] Define course-user pivot metadata to capture assistant assignments, enrollment roles, and per-course capabilities.
- [ ] Design DTOs with `spatie/laravel-data` for course/module/lesson payloads (creation, update, listing, publishing state).
- [ ] Identify lesson content types (rich text, embedded video URLs, attached documents) and the data structures needed to support them.
- [ ] Implement publishing workflow using datetime fields (`publish_at`) for modules and lessons; ensure only content past publish datetime is visible.
- [ ] Extend domain to support assignment modules, assignment submissions, exams, question banks, attendance records (present/late/absent), course titles, banner images (16:9), and rich course descriptions stored as Markdown in `description` columns.

## Permissions & Roles (`spatie/laravel-permission`)
- [ ] Publish and run the package's migrations once the core schema is defined; ensure configuration matches upcoming models.
- [ ] Seed base roles (`admin`, `teacher`, `content_manager`) and associated permissions; ensure default users without roles fall back to learner experience.
- [ ] Represent course assistants via course-user pivot metadata (no distinct role) granting content, grading, and attendance abilities limited to assigned courses.
- [ ] Map feature-level permissions: user management, course management (create/update/delete, assign teachers/assistants), enrollment, content publishing, attendance recording, exam management.
- [ ] Integrate policies and gates for all domain resources (courses, modules, lessons, assignments, attendance, exams, question banks) aligned with role and assistant capabilities.
- [ ] Ensure middleware/guards enforce authorization consistently across Fortify/Inertia flows.

## Course & Enrollment Management
- [ ] Outline actions (`app/Actions/...`) for creating/updating courses, assigning teachers, and enrolling users.
- [ ] Implement admin-only user provisioning that issues invitations and password reset links and can be resent by admins or teachers.
- [ ] Plan DTO-driven request validation layers (Form Requests) for course creation, teacher assignment, student enrollment.
- [ ] Specify how teachers can manage enrollments while respecting admin-only operations.
- [ ] Allow admins to revoke pending invitations when necessary.
- [ ] Support invitation acceptance/refusal workflows with invitations remaining active until responded to, auditing sent/responded timestamps, sender, recipient, and acceptance status, plus resending capabilities.
- [ ] Ensure content managers share admin abilities for course CRUD and assigning teachers/assistants while respecting scoped assistant permissions.
- [ ] Design pivot management for assigning/removing assistants per course with appropriate auditing.
- [ ] Implement bulk assistant assignment UI to attach/detach users across courses efficiently.

## Module & Lesson Authoring
- [ ] Design CRUD flows for modules (including assignment-type modules) and lessons with publish toggles; ensure only published content is visible to learners.
- [ ] Support unpublishing (revoking) lessons and modules after they have been published.
- [ ] Decide ordering strategy (manual ordering fields vs. drag-and-drop later).
- [ ] Prepare actions for attaching media to lessons via `spatie/laravel-medialibrary`.
- [ ] Evaluate need for draft vs. published versions and version history (MVP decision).

## Media & Content Handling (`spatie/laravel-medialibrary`)
- [ ] Configure media collections for lesson resources (documents, embedded video thumbnails if needed, supplemental files) and course banner images (enforce 16:9 ratio).
- [ ] Register `spatie/laravel-medialibrary` collections on models once those models are scaffolded.
- [ ] Determine storage disks, conversions (if any), and validation rules enforcing 5 MB max images and 10 MB max documents.
- [ ] Support allowed document extensions (PDF, spreadsheets, presentations, text documents); block direct video uploads while supporting embedded video metadata.
- [ ] Support multiple lesson attachments ordered by creation time; define retrieval queries respecting that order.
- [ ] Plan for embedding external video links vs. storing media locally.

## Frontend (Inertia + React)
- [ ] Outline course dashboard UI for admins/teachers/content managers/students including role-based navigation.
- [ ] Plan forms using `<Form>` or `useForm` helpers for course/module/lesson CRUD and enrollment, pairing them with Shadcn components for consistent UX.
- [ ] Integrate TipTap editor while persisting lesson body as Markdown for future editor flexibility (stored in `content` columns).
- [ ] Define lesson detail page to render rich text from stored Markdown, embedded media, and downloadable documents.
- [ ] Add UI flows for assignment analytics, submission review, and attendance recording/visualisation.
- [ ] Standardize data grids on TanStack Table with ShadCN UI components, including pagination, sorting, and filtering patterns.
- [ ] Build exam-taking interfaces with autosave per question, countdown timers, and status indicators; include configuration screens for question banks.
- [ ] Consider deferred props or lazy loading for large media lists or lesson content.
- [ ] Emphasize Tailwind CSS v4 utilities and design tokens alongside Shadcn primitives for layout, spacing, and theming.

## Testing Strategy (Pest)
- [ ] Draft feature tests covering role permissions, course lifecycle, enrollment flows, publish visibility, exams, and assignments.
- [ ] Create datasets for validation scenarios (e.g., invalid media uploads, enrollment constraints).
- [ ] Include media handling tests leveraging temporary storage where needed.
- [ ] Add automated grading and autosave behaviour tests for exams (unit + feature).

## Assignments & Submissions
- [ ] Define assignment module structure supporting attachments, deadlines, manual enable, and datetime-based availability.
- [ ] Design submission model capturing student attachments, rich text responses, timestamps, grades, and per-student deadline extensions.
- [ ] Plan DTOs for assignments and submissions to power admin/teacher/content manager/assistant authoring and learner submissions.
- [ ] Ensure media handling rules align with assignment attachment requirements.
- [ ] Build actions for assignment creation, publishing, grading (including analytics dashboards), and submission handling (including validation and deadline overrides).
- [ ] Determine data model for storing assignment analytics (submission status counts, grades, timestamps, attached artifacts) accessible to teachers, content managers, and assigned assistants.
- [ ] Trigger student notification when a grade is published while leaving other assignment notifications out of scope for MVP.

## Exams & Question Banks
- [ ] Design models for exams, exam sections, questions (single choice, multiple choice, rich text), question banks, and exam attempts.
- [ ] Support configurable question counts per exam including random selection from question banks.
- [ ] Implement scheduling windows (start/end datetimes) and time limits for exam attempts.
- [ ] Determine autosave strategy for student responses (per question) using Inertia/React with backend persistence.
- [ ] Build auto-grading for single/multiple choice exams; require manual grading when rich text questions exist.
- [ ] Create DTOs and actions for exam creation, configuration, question bank management, and grading workflows.
- [ ] Ensure teachers, assistants, and content managers can create/configure exams and question banks within assigned courses.
- [ ] Plan analytics for exams (completion status, auto-graded scores, manual grading queues).

## Attendance Tracking
- [ ] Model manually created attendance sessions for each course with statuses (`present`, `late`, `absent`) tied to students and timestamps.
- [ ] Build actions that allow teachers, assistants, and content managers to create sessions, record attendance, update entries, and maintain history.
- [ ] Ensure admins, teachers, assistants, and content managers can view attendance records; define learner visibility requirements if any.
- [ ] Plan DTOs and Inertia pages for capturing attendance and rendering summaries/analytics.
- [ ] Consider future integration with `spatie/laravel-settings` if attendance policies need to be configurable per course or globally.

## TypeScript & DTO Synchronization
- [ ] Integrate `spatie/typescript-transformer` to generate TypeScript typings from DTOs for frontend usage.
- [ ] Determine generation workflow (Artisan command, build step) and configure paths for Inertia React components.
- [ ] Ensure DTO changes trigger regenerated types and include developer documentation within TODO until implemented.

## Operational Considerations
- [ ] Review need for background jobs (e.g., media processing) and queue configuration.
- [ ] Ensure seeding strategies create demo data for courses/modules/lessons/assignments/exams.
- [ ] Decide on audit/logging requirements for content changes (optional for MVP?).
- [ ] Store all datetimes in UTC and ensure frontend displays them according to user locale preferences.
- [ ] Default to Laravel's plain-text mail templates with minimal branding for invitations and notifications.
- [ ] Evaluate future use of `spatie/laravel-settings` for configurable app-wide options (attendance policies, grading scales, etc.).
- [ ] Plan queue/worker usage for heavy exam operations (auto-grading batches) if needed.
- [ ] Ensure autosave frequency and exam timing constraints are configurable via settings when `spatie/laravel-settings` is introduced.
