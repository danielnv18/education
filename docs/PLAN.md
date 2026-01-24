# LMS Implementation Plan

## Scope Inputs
- `docs/ARCHITECTURE.md`
- `docs/SCHEMA.md`
- `docs/PAGES.md`
- `docs/TODO.md`

## Guiding Principles
- Whenever creating or updating an Eloquent model, create or update its factory and corresponding DTOs in the same phase to keep backend payloads and tests aligned.
- Maintain phase-level Pest coverage; tests accompany each feature milestone rather than being deferred.
- Soft deletes are the default for every domain model—add the `deleted_at` column in migrations and apply the `SoftDeletes` trait when scaffolding models and actions.

## Phase 1 — Foundation & Tooling
- [x] Confirm base packages (`spatie/laravel-permission`, `spatie/laravel-medialibrary`, `spatie/laravel-data`, `spatie/laravel-typescript-transformer`) are installed and configured. Publish vendor migrations that are prerequisites (`roles`, `permissions`, `media`).
- [x] Establish shared enums in PHP (`CourseStatus`, `CourseRole`, `EnrollmentStatus`, etc.) to match `docs/ARCHITECTURE.md` and wire them into `config`/`app/Enums`.
- [x] Prepare developer tooling flows: DTO generation pipeline, Pint formatting script, Pest helpers, and queues configuration (per `docs/TODO.md` Operational Considerations).
- [x] Add baseline Pest tests (configuration smoke tests, enum serialization checks) to ensure tooling and enums behave as expected.

## Phase 2 — Users, Roles & Invitations
1. Create the `user_profiles` table, model, factory, DTOs, and seed data; connect to `User` relationships (see `docs/SCHEMA.md` Core User Enhancements).
2. Wire `HasRoles` and seed base roles/permissions (`admin`, `content_manager`, `teacher`). Document exact permission matrix.
3. Implement invitation domain: migration, model, actions for issuing/resending/revoking invitations, and Fortify hooks (per `docs/ARCHITECTURE.md` Invitation Flow and `docs/TODO.md` Course & Enrollment Management).
4. Deliver Pest feature tests for invitations, role assignments, and Fortify integration (registration/login guards, invitation acceptance).

## Phase 3 — Courses & Enrollment
- [x] Build course migrations/models/factories with metadata/publishing columns (`docs/SCHEMA.md` Course Structure).
2. Build DTOs for courses.
3. [x] Implement `CourseUser` pivot with single-teacher constraint and role scopes.
4. Implement actions for attaching teachers/assistants/students.
3. Add policies for course visibility and enrollment management, aligning with assistants' capabilities (refer to `docs/ARCHITECTURE.md` Domain Roles & Relationships).
4. Write Pest tests validating course ownership rules, enrollment role scopes, and policy enforcement.

## Phase 4 — Modules & Lessons
1. [x] Scaffold module and lesson migrations/models/factories with ordering, publish windows, and support for mixed module content (`docs/SCHEMA.md` Modules & Lessons).
2. Scaffold module and lesson DTOs.
2. Integrate media collections for lessons and course banners via Medialibrary; add attachment actions (per `docs/TODO.md` Media & Content Handling).
3. Create actions/form requests for CRUD, publishing toggles, and scheduling validation. Ensure Inertia props use DTOs.
4. Cover module/lesson CRUD, scheduling validation, and media rules with Pest feature tests (including attachment upload handling).

## Phase 5 — Assignments & Submissions
1. Implement migrations/models/factories/DTOs for assignments, extensions, submissions, and submission events (`docs/SCHEMA.md` Assignments & Submissions).
2. Build actions covering assignment authoring, publishing, grading, and submission handling; include notification triggers (see `docs/TODO.md` Assignments & Submissions).
3. Expose Inertia endpoints and pages for authoring and learner submission flows as outlined in `docs/PAGES.md` Assignments & Submissions.
4. Add Pest tests for assignment workflows: creation, publishing windows, submission lifecycle, grading feedback, and notifications.

## Phase 6 — Exams & Question Banks
1. Create migrations/models/factories/DTOs for question banks, questions, options, exams, sections, attempts, and responses (`docs/SCHEMA.md` Exams & Question Banks).
2. Implement autosave and grading workflows in actions, ensuring debounce behavior matches `docs/ARCHITECTURE.md` Exam Attempt Autosave.
3. Deliver exam authoring/review Inertia pages per `docs/PAGES.md` Exams & Question Banks; integrate analytics and grading queues.
4. Ensure Pest tests cover exam authoring, autosave persistence, grading paths (auto/manual), and attempt policies.

## Phase 7 — Attendance Tracking
1. Add attendance session and entry tables/models/factories/DTOs, enforcing unique constraints (reference `docs/SCHEMA.md` Attendance Tracking).
2. Provide actions for session creation and status updates plus authorization policies.
3. Ship attendance management UI (sessions list, record view, analytics) from `docs/PAGES.md` Attendance, including per-student aggregates (percent present, counts of present/late/absent) for teachers/assistants and personal history views for learners.
4. Write Pest tests validating attendance session creation, status transitions, analytics visibility, and permissions (teacher/assistant roster summaries, student history views).

## Phase 8 — Notifications, Analytics & Settings
1. Implement notification logging infrastructure and optional audit trail (per `docs/SCHEMA.md` Notifications & Auditing Support).
2. Define background jobs for media conversions, notifications, and prospective grading batches (`docs/ARCHITECTURE.md` Media Processing).
3. Evaluate configuration needs (`spatie/laravel-settings`) for future-proofing autosave intervals, grading policies, and attendance preferences (`docs/TODO.md` Operational Considerations).
4. Create Pest tests for notification logging, audit trails, and background job dispatch/queue interactions.

## Phase 9 — Frontend Integration & UX Polish
1. Build shared layout/navigation, dashboard, notifications center, and role switchers (`docs/PAGES.md` Global Experience & Administration).
2. Implement course shell tabs, module/lesson editors, and shared UI primitives using Shadcn + Tailwind v4 (`docs/ARCHITECTURE.md` Frontend Architecture Notes).
3. Ensure deferred props, polling, and upload progress adhere to Inertia guidelines, and keep DTO-generated TypeScript definitions in sync.
4. Add Pest browser/feature tests exercising navigation, shared props, polling/deferred behaviors, and critical upload flows.

## Phase 10 — Testing, QA & Launch Readiness
1. Aggregate coverage reports and ensure test suites for each prior phase remain green; fill any gaps discovered during final review.
2. Seed demo data and verify through browser smoke tests; capture outstanding bugs or UX gaps, especially around analytics summaries.
3. Finalize deployment checklist: queues, storage disks, email templates, and monitoring hooks.

## Open Questions
- Clarify minimum viable scope for analytics (assignments/exams) and notification triggers beyond grading confirmations.
