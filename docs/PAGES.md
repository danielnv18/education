# LMS Page Plan

## Design Principles
- Use Shadcn UI primitives (cards, tables, forms, dialogs, breadcrumbs) combined with Tailwind CSS v4 utilities for layout, spacing, and typography.
- Keep responsive breakpoints consistent with existing layouts; prefer CSS grid/flex utilities with `gap-*` over custom margins.
- Surface enum-driven statuses using shared badge components that map PHP enums to consistent color tokens.
- Lean on Inertia capabilities for everything lifecycle related: use `<Form>` / `useForm` for submissions, `<Link>` for navigation (with `prefetch` where helpful), `router.visit()` for programmatic transitions, and `router.reload()` with partial reload options to keep pages reactive.
- Prefer Inertia shared props (via the middleware) for global flash messages, auth context, nav data, and unread counts instead of duplicating API calls client-side.
- When fetching large payloads, default to deferred props or lazy `whenVisible` loading so initial responses stay small; pair deferred experiences with skeleton states.
- Use Inertia file upload support (forms automatically switch to `FormData`) and track progress via `router.on('progress')` if uploads are significant (course banners, lesson attachments, submissions).
- Apply `usePoll` on grading queues and timers where live updates are needed; throttle by business need and stop polling when drawers/dialogs close. Avoid polling for notification counts to prevent unnecessary server load.

## Global Experience
- **Landing / Sign-in:** Fortify-backed authentication screen using Shadcn `Card`, `Input`, and `Button`. Include quick links for password reset and invitations. Submit with `<Form>` to benefit from Inertia validation handling and shared flash messaging.
- **Dashboard (role-aware):** Default page after login displaying cards for active courses, pending tasks, and announcements. Use Shadcn `Tabs` or segmented controls to switch between roles (admin, teacher, assistant, learner). Pull summaries via deferred props and refresh active tabs with partial reloads rather than bespoke fetch hooks.
- **Notifications Center:** List invitation statuses, grading alerts, and assignment reminders using Shadcn `Badge` + `Accordion` for grouping. Surface unread counts via shared props and manual soft refreshes, avoiding continuous polling.

```text
/
├─ /login
├─ /password/reset
├─ /dashboard
└─ /notifications
```

## Administration
- **User Management:** Table (TanStack + Shadcn `Table`) with filters, role assignment controls (global roles: admin, content manager). Modal dialog for inviting new users and resending invitations. Use `<Form>` with partial reloads targeting `users`, and prefetch detail rows before entering modals.
- **Course Index:** Grid/list of all courses with status badges (enum-driven). Include bulk actions for publishing, archiving, and assigning primary owners. Use `router.reload({ only: [...] })` to refresh affected badges instead of full page visits.
- **Settings:** Placeholder for future global preferences (e.g., default timezones, grading scales) aligning with potential `spatie/laravel-settings` usage. Persist via Inertia forms and surface confirmations using shared flash props.

```text
/admin
├─ /admin/users
│  └─ /admin/users/{user}
├─ /admin/courses
│  └─ /admin/courses/{course}
└─ /admin/settings
```

## Course Authoring
- **Course Detail Shell:** Header with banner image management (Shadcn `Dialog` for uploads), tabs for Modules, People, Attendance, Assignments, Exams. Uploads run through Inertia form submissions with progress bars; tab content should defer heavy payloads and rely on partial reloads when actions occur.
- **People Tab:** Two sections—teachers/assistants (driven by pivot) and student roster. Assistants gain full course-management capabilities for each course they’re assigned to (no granular overrides). Surface per-student attendance summaries (percent present, counts of present/late/absent) inline for teachers and assistants. Use `whenVisible` or deferred props so large rosters fetch only on demand.
- **Enrollment Flows:** Invite learners via email using Shadcn `Sheet` or modal containing `<Form>` fields and enum-backed status display for pending invitations. Prefetch invitation endpoints and use partial reloads to update local lists instantly.

```text
/courses
├─ /courses/{course}
│  ├─ /courses/{course}/people
│  ├─ /courses/{course}/enrollments
│  ├─ /courses/{course}/attendance
│  ├─ /courses/{course}/assignments
│  └─ /courses/{course}/exams
└─ /courses/create
```

## Module & Lesson Builder
- **Module List:** Ordered list with drag-and-drop placeholder for future enhancements; currently use buttons for `Move Up/Down`. Include publish scheduling inputs using Shadcn `Popover` date/time pickers. After reordering, trigger partial reloads scoped to `modules` instead of reloading the whole course shell.
- **Lesson Editor:** Two-pane layout (form + preview). Integrate TipTap editor for Markdown content, Shadcn `ToggleGroup` for content type selection, and media attachment panel leveraging `Attachment` component. Handle uploads with Inertia form data, display progress via `router.on('progress')`, and debounce autosave with `router.reload({ preserveScroll: true })` as lessons don’t require continuous polling.
- **Module Detail:** Summary card with publish window status, nested lesson list, and quick actions for duplication/unpublish. Defer loading of lesson analytics until the panel is opened.

```text
/courses/{course}/modules
├─ /courses/{course}/modules/create
├─ /courses/{course}/modules/{module}
│  ├─ /courses/{course}/modules/{module}/edit
│  └─ /courses/{course}/modules/{module}/lessons/create
└─ /courses/{course}/modules/{module}/lessons/{lesson}/edit
```

## Assignments & Submissions
- **Assignments Index:** Table filtered by module/course with status badges (`draft`, `published`). Include bulk publish/unpublish operations. Prefetch row actions and rely on partial reloads for the `assignments` prop after bulk changes.
- **Assignment Editor:** Shadcn `Stepper` or segmented control to switch between instructions, settings (points, windows), and attachments. Provide per-student extension management via nested dialog. Use `<Form>` for wizard steps and flush extension tables with deferred props so late data loads async.
- **Submission Review:** Split view with submission metadata (enum status, timestamps) and content preview; include grading form using Shadcn `Textarea`, `Input`, and `Select`. Use shared flash for grading confirmations and trigger manual partial reloads or event broadcasting if concurrent graders are expected.
- **Learner Submission Page:** Timeline of submission events, upload widget (drag-and-drop), and status indicator banner. Track upload progress for large artifacts and offer manual refresh affordances; polling is not required once uploads finish.

```text
/courses/{course}/assignments
├─ /courses/{course}/assignments/create
├─ /courses/{course}/assignments/{assignment}
│  ├─ /courses/{course}/assignments/{assignment}/edit
│  ├─ /courses/{course}/assignments/{assignment}/extensions
│  └─ /courses/{course}/assignments/{assignment}/submissions/{submission}
└─ /assignments/{assignment}/submit
```

## Exams & Question Banks
- **Question Bank Manager:** Tree/table view of banks. Provide Shadcn `Accordion` for sections within a bank, and inline forms for prompt editing. Use partial reloads for section edits and prefetch question detail routes.
- **Question Editor:** Form tailored to question type with dynamic fields; use Shadcn `Tabs` or `SegmentedControl` for answer options vs. metadata. Submit via `<Form>` components and persist file-based illustrations with upload progress indicators.
- **Exam Builder:** Multi-step screen for scheduling, section configuration, and question selection. Include summary sidebar showing estimated duration and attempt policies. Defer heavy question lists until the relevant step is active, and prefetch review routes for faster transitions.
- **Exam Attempt Interface:** Full-screen layout with sticky header (timer, progress). Use Shadcn `ScrollArea` for question list and ensure autosave status indicator. Autosave immediately when select-type answers change; throttle rich-text answers to save 30 seconds after typing stops (resetting the timer with each keystroke).
- **Grading Queue:** List of attempts awaiting manual grading, filtered by course/module with status chips. Poll for new attempts and use shared data to surface queue counts in navigation.

```text
/courses/{course}/question-banks
├─ /courses/{course}/question-banks/create
├─ /courses/{course}/question-banks/{bank}
│  └─ /courses/{course}/question-banks/{bank}/questions/{question}
/courses/{course}/exams
├─ /courses/{course}/exams/create
├─ /courses/{course}/exams/{exam}
│  ├─ /courses/{course}/exams/{exam}/edit
│  ├─ /courses/{course}/exams/{exam}/sections/{section}
│  └─ /courses/{course}/exams/{exam}/grading
└─ /exams/{exam}/attempts/{attempt}
```

## Attendance
- **Session List:** Table showing past sessions with status counts; quick action to open a session in record mode. Prefetch the record view and refresh summaries via partial reloads when statuses change.
- **Record Attendance:** Grid of students with quick status toggles (present/late/absent) mapped to enum badge colors; include notes drawer per student. Persist edits through Inertia forms with `preserveScroll`; for concurrent edits, rely on server events or manual refresh triggers rather than polling.
- **Attendance Analytics:** Charts/tables highlighting trends and totals leveraging Shadcn `Card`/`Chart` components (if available). Provide aggregated stats (percent present, counts per status) for each learner within the course roster (teacher/assistant access) and expose a personal history view for students. Use deferred props for expensive aggregates so the sessions list renders instantly.

```text
/courses/{course}/attendance
├─ /courses/{course}/attendance/sessions/create
├─ /courses/{course}/attendance/sessions/{session}
└─ /courses/{course}/attendance/analytics
```

## Learner Experience
- **My Courses:** Card layout showing publish status and next lesson. Provide CTA for latest assignment/exam due soon and quick access to personal attendance/assessment statistics. Prefetch course pages on hover and rely on shared data for global deadlines.
- **Lesson Reader:** Focused view with breadcrumb navigation, Markdown-rendered content, media attachments, and publish state alerts. Lazy-load attachments via deferred props and track download progress for large files. Offer a sidebar summary of the student’s standing (attendance percentage, assignment/exam completion) within the course.
- **Assignment Detail:** Shows instructions, due dates, submission status (enum). Provide direct link to submit/edit attempt. Use partial reloads to refresh status after submission without leaving the page and link to attendance/assessment analytics for the student.
- **Exam Overview:** Displays availability windows, attempt limits, and start CTA; disables button when outside schedule according to enum-driven status. Poll for window changes in the minutes leading up to availability to keep CTAs accurate.

```text
/my/courses
├─ /my/courses/{course}
│  ├─ /courses/{course}/lessons/{lesson}
│  ├─ /courses/{course}/assignments/{assignment}
│  └─ /courses/{course}/exams/{exam}
└─ /my/tasks
```

## Support Pages
- **Invitations:** Acceptance/decline interface showing course context and role implications. Submit invitation responses through `<Form>` to reuse shared flash messaging.
- **Error / Empty States:** Standardised Shadcn `Card` with iconography for 403/404/maintenance plus skeleton loaders for deferred Inertia props. Prefetch retry links to reduce friction when navigating away.

```text
/invitations/{token}
/error/403
/error/404
/maintenance
```
