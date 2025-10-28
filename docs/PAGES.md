# LMS Page Plan

## Design Principles
- Use Shadcn UI primitives (cards, tables, forms, dialogs, breadcrumbs) combined with Tailwind CSS v4 utilities for layout, spacing, and typography.
- Keep responsive breakpoints consistent with existing layouts; prefer CSS grid/flex utilities with `gap-*` over custom margins.
- Surface enum-driven statuses using shared badge components that map PHP enums to consistent color tokens.
- Lean on Inertia form helpers (`<Form>` / `useForm`) for submission state handling, integrating Shadcn feedback elements for errors and success notices.

## Global Experience
- **Landing / Sign-in:** Fortify-backed authentication screen using Shadcn `Card`, `Input`, and `Button`. Include quick links for password reset and invitations.
- **Dashboard (role-aware):** Default page after login displaying cards for active courses, pending tasks, and announcements. Use Shadcn `Tabs` or segmented controls to switch between roles (admin, teacher, assistant, learner).
- **Notifications Center:** List invitation statuses, grading alerts, and assignment reminders using Shadcn `Badge` + `Accordion` for grouping.

```text
/
├─ /login
├─ /password/reset
├─ /dashboard
└─ /notifications
```

## Administration
- **User Management:** Table (TanStack + Shadcn `Table`) with filters, role assignment controls (global roles: admin, content manager). Modal dialog for inviting new users and resending invitations.
- **Course Index:** Grid/list of all courses with status badges (enum-driven). Include bulk actions for publishing, archiving, and assigning primary owners.
- **Settings:** Placeholder for future global preferences (e.g., default timezones, grading scales) aligning with potential `spatie/laravel-settings` usage.

```text
/admin
├─ /admin/users
│  └─ /admin/users/{user}
├─ /admin/courses
│  └─ /admin/courses/{course}
└─ /admin/settings
```

## Course Authoring
- **Course Detail Shell:** Header with banner image management (Shadcn `Dialog` for uploads), tabs for Modules, People, Attendance, Assignments, Exams.
- **People Tab:** Two sections—teachers/assistants (driven by pivot) and student roster. Include assistant scope editor using Shadcn `Drawer` for capability toggles.
- **Enrollment Flows:** Invite learners via email using Shadcn `Sheet` or modal containing `<Form>` fields and enum-backed status display for pending invitations.

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
- **Module List:** Ordered list with drag-and-drop placeholder for future enhancements; currently use buttons for `Move Up/Down`. Include publish scheduling inputs using Shadcn `Popover` date/time pickers.
- **Lesson Editor:** Two-pane layout (form + preview). Integrate TipTap editor for Markdown content, Shadcn `ToggleGroup` for content type selection, and media attachment panel leveraging `Attachment` component.
- **Module Detail:** Summary card with publish window status, nested lesson list, and quick actions for duplication/unpublish.

```text
/courses/{course}/modules
├─ /courses/{course}/modules/create
├─ /courses/{course}/modules/{module}
│  ├─ /courses/{course}/modules/{module}/edit
│  └─ /courses/{course}/modules/{module}/lessons/create
└─ /courses/{course}/modules/{module}/lessons/{lesson}/edit
```

## Assignments & Submissions
- **Assignments Index:** Table filtered by module/course with status badges (`draft`, `published`). Include bulk publish/unpublish operations.
- **Assignment Editor:** Shadcn `Stepper` or segmented control to switch between instructions, settings (points, windows), and attachments. Provide per-student extension management via nested dialog.
- **Submission Review:** Split view with submission metadata (enum status, timestamps) and content preview; include grading form using Shadcn `Textarea`, `Input`, and `Select`.
- **Learner Submission Page:** Timeline of submission events, upload widget (drag-and-drop), and status indicator banner.

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
- **Question Bank Manager:** Tree/table view of banks. Provide Shadcn `Accordion` for sections within a bank, and inline forms for prompt editing.
- **Question Editor:** Form tailored to question type with dynamic fields; use Shadcn `Tabs` or `SegmentedControl` for answer options vs. metadata.
- **Exam Builder:** Multi-step screen for scheduling, section configuration, and question selection. Include summary sidebar showing estimated duration and attempt policies.
- **Exam Attempt Interface:** Full-screen layout with sticky header (timer, progress). Use Shadcn `ScrollArea` for question list and ensure autosave status indicator.
- **Grading Queue:** List of attempts awaiting manual grading, filtered by course/module with status chips.

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
- **Session List:** Table showing past sessions with status counts; quick action to open a session in record mode.
- **Record Attendance:** Grid of students with quick status toggles (present/late/absent) mapped to enum badge colors; include notes drawer per student.
- **Attendance Analytics:** Charts/tables highlighting trends and totals leveraging Shadcn `Card`/`Chart` components (if available).

```text
/courses/{course}/attendance
├─ /courses/{course}/attendance/sessions/create
├─ /courses/{course}/attendance/sessions/{session}
└─ /courses/{course}/attendance/analytics
```

## Learner Experience
- **My Courses:** Card layout showing publish status and next lesson. Provide CTA for latest assignment/exam due soon.
- **Lesson Reader:** Focused view with breadcrumb navigation, Markdown-rendered content, media attachments, and publish state alerts.
- **Assignment Detail:** Shows instructions, due dates, submission status (enum). Provide direct link to submit/edit attempt.
- **Exam Overview:** Displays availability windows, attempt limits, and start CTA; disables button when outside schedule according to enum-driven status.

```text
/my/courses
├─ /my/courses/{course}
│  ├─ /courses/{course}/lessons/{lesson}
│  ├─ /courses/{course}/assignments/{assignment}
│  └─ /courses/{course}/exams/{exam}
└─ /my/tasks
```

## Support Pages
- **Invitations:** Acceptance/decline interface showing course context and role implications.
- **Error / Empty States:** Standardised Shadcn `Card` with iconography for 403/404/maintenance plus skeleton loaders for deferred Inertia props.

```text
/invitations/{token}
/error/403
/error/404
/maintenance
```
