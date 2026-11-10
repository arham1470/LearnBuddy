# LearnBuddy --- Incremental Antigravity Development Prompt Pack

## Sri Lanka-based LMS \| Laravel \| Aiven MySQL \| Responsive \| Security-first

This pack converts the original LearnBuddy master specification into
sequential prompts. Use **one prompt at a time** in Antigravity. Do not
paste all prompts together.

## How to use this pack

For every phase:

1.  Open the existing LearnBuddy project in Antigravity.
2.  Paste only the current phase prompt.
3.  Let Antigravity implement only that phase.
4.  Review every changed file and the explanation.
5.  Run the manual test checklist yourself in the browser/terminal. Do
    not ask another AI to perform or certify this manual test.
6.  If a test fails, report the exact failure to Antigravity and fix it
    within the current phase.
7.  When all checks pass, commit the phase using the suggested Git
    commit message.
8.  Only then paste the next phase prompt.

**Never proceed just because Antigravity says "completed".** You are the
person who approves each phase.

### Suggested Git routine

Before starting a phase:

``` bash
git status --short
git branch --show-current
# Review and stage only the intended checkpoint files; do not blindly stage everything.
git add <reviewed-files>
git diff --cached --check
git diff --cached
git commit -m "chore: checkpoint before phase N"
```

After a phase passes your manual tests:

``` bash
git status --short
git diff --check
# Stage only files belonging to this completed phase.
git add <reviewed-files>
git diff --cached --check
git diff --cached
git commit -m "feat(scope): short description of completed phase"
```

Do not commit `.env`, passwords, API keys, private certificates,
uploaded private media, or other secrets. Check `.gitignore` and
`git status` before every commit. If a phase changes the database
schema, keep its migration files in the commit. Do not run destructive
production migrations without review and a backup.

------------------------------------------------------------------------

# PROMPT 0 --- PROJECT RULES (KEEP THESE IN CONTEXT)

Paste this prompt at the beginning of the project. If Antigravity loses
context in a new session, paste it again before the next phase.

``` text
Act as a Senior Software Architect, Senior Laravel Developer, UI/UX Designer, Database Architect and Application Security Engineer.

We are building LearnBuddy, a real, commercial-quality Learning Management System (LMS), not a screenshot-only prototype. It should provide a polished and easy-to-use learning experience comparable in usability to Udemy while retaining its own identity.

PROJECT LOCATION AND MARKET
- LearnBuddy is primarily designed for users, students, instructors and education providers in Sri Lanka.
- Use Sri Lanka as the default market and context throughout the product.
- Use LKR (Sri Lankan rupees) as the default display and transaction currency. Store monetary values safely using decimal precision and a currency code; do not use floating-point arithmetic for money.
- Use the Asia/Colombo time zone for display, scheduling, reminders and date/time interpretation. Store timestamps consistently and convert to Sri Lankan local time for users.
- Design Sri Lankan address fields appropriately (province, district, city/town and address lines) without assuming every address has the same structure.
- Support Sri Lankan telephone numbers, including +94 country code, with sensible validation and display. Do not make phone number the sole account identifier unless explicitly approved.
- Plan for English as the initial interface language and structure the application so Sinhala and Tamil localisation can be added without rewriting pages. Do not invent translations; request approved translations when needed.
- Keep tax, service charges, invoice details and payment-provider settings configurable. Do not hardcode tax rates or make unsupported legal claims.
- Evaluate payment gateways that can serve Sri Lankan businesses, such as locally available providers, but do not integrate a provider or claim eligibility until current availability, merchant requirements, fees and settlement support have been checked and the owner approves it.
- Consider Sri Lankan privacy and data-protection obligations, including the Personal Data Protection Act and applicable amendments/regulations. Treat this as an engineering consideration, not legal advice; flag questions requiring professional legal review.
- Courses may be offered to local or international students later. Do not make the database incapable of supporting additional currencies or countries.

PRODUCT CAPABILITIES
- Free and paid courses.
- Online, physical classroom and hybrid learning.
- A single course can provide multiple delivery options without duplicating the course.
- Recorded video lessons and interactive live classes.
- Assignments, quizzes, progress tracking, certificates, reviews, ratings, notifications and announcements.

TECHNOLOGY — DO NOT CHANGE WITHOUT EXPLICIT APPROVAL
Backend: Laravel, PHP, MySQL, Eloquent ORM, Laravel Form Requests, Policies and Gates.
Frontend: Laravel Blade, Tailwind CSS, Alpine.js where appropriate, and JavaScript for required interactions.
Authentication: Laravel Breeze or a suitable Laravel authentication solution.
Administration: Filament, customised to LearnBuddy branding.
Local development: Windows with Laragon or XAMPP; Git.
Avoid adding React, Node.js as a separate application architecture, unnecessary frameworks or packages without a strong technical reason and approval.

DATABASE
The production-intended primary database is Aiven MySQL. Do not silently replace it with SQLite, Supabase, Firebase or another database. Use Laravel migrations, Eloquent relationships, foreign keys, indexes, unique constraints, transactions for multi-step operations, decimal money fields, duplicate-enrolment prevention, physical-batch overbooking prevention, and safe archival/deletion rules.

AIVEN CONNECTION SETTINGS
- Engine: MySQL
- Database: defaultdb
- Host: mysql-3e258d69-fwvhaqc2992786-b39c.k.aivencloud.com
- Port: 23001
- Username: avnadmin
- Password: environment secret only; never put it in source code, prompts, logs, documentation or Git.
- SSL: REQUIRED.
Use an environment variable such as AIVEN_DB_PASSWORD. Obtain the correct CA certificate from Aiven and configure Laravel PDO SSL CA options in config/database.php. Do not disable SSL or silently fall back to unencrypted connections. Do not assume DB_HOST alone is sufficient. Restrict inbound connections where possible, isolate development and production, back up data and never expose connection details in errors.
If the real password has ever been shared in chat, source code or a commit, advise the owner to rotate it. Never ask the owner to paste it into chat or commit it.

BRAND AND EXISTING ASSETS
- Product name: LearnBuddy.
- Inspect and use the official logo from `logo/`; do not invent or replace it. Preserve its proportions and use appropriate variants.
- Inspect `refrence_ui/` and preserve its typography, layout, spacing, colours, navigation, cards, buttons and interactions as closely as reasonably possible. Extend it consistently. Never replace it with a generic AI dashboard.
- Exact CSS palette:
  --orange: #FF5F00;
  --orange-light: #FF8A3D;
  --orange-dark: #D94E00;
  --white: #FFFFFF;
  --off-white: #F7F7F5;
  --gray-light: #E5E5E5;
  --gray-mid: #6B6B6B;
  --black: #0A0A0A;
  --near-black: #1A1A1A;
Orange is the primary brand colour. Use white/off-white backgrounds, dark text and grey borders/secondary information. Maintain accessible contrast. Avoid unnecessary colours and excessive gradients.

DESIGN QUALITY
Premium, professional, clean, minimal, easy to navigate, clear hierarchy, consistent spacing, excellent typography, carefully designed cards/forms, purposeful animation and fast accessible interactions.
Avoid generic AI designs, excessive glassmorphism/gradients/animations, overcrowded dashboards, random icons, repetitive card layouts and excessive rounded corners. Do not sacrifice usability for effects.

RESPONSIVE DESIGN IS MANDATORY ON EVERY PAGE
All public, student, instructor, admin and learning pages must work on mobile, tablet and desktop. Use mobile-first principles where appropriate, preserve the reference UI, use Tailwind responsive utilities and reusable components, and prevent horizontal overflow.
Check at minimum:
- 360 × 800
- 390 × 844
- 430 × 932
- 768 × 1024
- 1024 × 768
- 1366 × 768
- 1920 × 1080
Check overflow, broken/overlapping layouts, text wrapping, navigation, touch targets, forms, modals, tables, video players, loading and empty states.
Mobile navigation must open/close and support touch dropdowns. Homepage/catalogue hero, cards, search and filters must adapt. Forms need full-width inputs where appropriate, readable validation and touch-friendly controls. Student dashboard navigation, cards, progress, tables, classes and assignments must remain usable. Learning video, curriculum, lesson navigation, resources, assignments and supported live-class joining must work on mobile. Admin/instructor tables, filters, action menus and forms must be usable on small screens. Do not call a page complete until responsive behaviour is manually checked.

ROLES
1. Administrator
2. Instructor
3. Student
Enforce permissions on the server using policies/gates and least privilege. Hiding a button in the frontend is not authorisation.

ADMINISTRATOR CAN
Manage users/instructors, courses/categories, prices/delivery options, batches/locations, videos/live classes, enrolments/payments, assignments/quizzes, certificates, reports and system settings.

INSTRUCTOR CAN
Manage only authorised courses, sections/lessons/materials, live classes, assignments/quizzes, student submissions, attendance and learning progress.

STUDENT CAN
Discover courses, enrol in free/paid courses, access authorised lessons, attend live/physical classes, submit assignments, complete quizzes, track progress, receive notifications and obtain eligible certificates.

COURSES AND ENROLMENT
A course supports Online, Physical or Both. Students select a delivery method before enrolment. Each option can have its own price, availability, capacity, schedule, location, batch and access conditions.
Course fields: title, slug, description, thumbnail, category, instructor(s), learning outcomes, prerequisites, difficulty, language, duration, pricing, delivery modes, curriculum and publication status.
Lifecycle: Draft → Review → Published → Archived.
Enrolment flow: select course → view details → choose Online/Physical → select available physical batch if needed → server validates current price and availability → confirm → free enrolment is created after validation, or paid enrolment activates only after verified payment → confirmation → course appears in dashboard.
Prevent duplicate enrolment and batch-capacity race conditions.

STUDENT EXPERIENCE
Public pages: homepage, catalogue, search/filters, course details, instructor profiles, reviews, registration, login and password recovery.
Dashboard: enrolled courses, continue learning, upcoming live classes, physical schedule, pending assignments, quiz activity, progress, certificates, notifications and recent activity.
Learning screen: recorded video, curriculum, lesson navigation, completion tracking, notes, resources, assignments, quizzes and discussions where appropriate.

LIVE CLASSES
Evaluate Jitsi Meet for the initial hosted live-class implementation. A session has course, instructor, title, description, start/end time, provider, meeting identifier and status. Only authorised enrolled students may join. Support scheduling, rescheduling, cancellation, upcoming/completed sessions, reminders and instructor access. Never expose private meeting credentials or administrative controls to students.

VIDEO
Evaluate Mux for recorded video. Require secure direct uploads, private assets, webhooks, processing status, signed playback access, short-lived tokens, enrolment checks, lesson-level authorisation, HLS playback and watermarking where appropriate. No permanent private tokens in frontend code and no public storage for private videos. Prevent IDOR by checking authorisation on every protected playback request. Do not claim screen recording can be completely prevented.

EXTERNAL SERVICES
Prefer free tiers/open source and verify current limits; never assume unlimited or permanently free. Evaluate:
- Mux: recorded video
- Jitsi Meet: live classes
- Cloudflare R2: course resources
- Resend: transactional email
- Firebase Cloud Messaging: push notifications
- Aiven: MySQL
Use replaceable service interfaces. Do not introduce paid services without owner approval. For Sri Lankan payments, research provider eligibility, fees, settlement, currency support and merchant requirements before proposing implementation. Use sandbox mode until production credentials are provided.

PAYMENTS
Provider-independent architecture: orders, course prices, transaction references, payment status/history, receipts, refund status and failed payments.
Never store card numbers or CVV. Verify server-side, validate webhook signatures, use idempotency and transactions, prevent duplicate enrolment activation and retain audit records.

NOTIFICATIONS
Use Laravel Notifications and queued jobs. Support registration verification, password resets, enrolment/payment confirmation, live-class reminders, rescheduling/cancellation, assignment notifications, quiz results, certificates and course announcements. Include in-app read/unread state and prevent duplicate reminders.

DATABASE DOMAIN TO PLAN
users, roles, permissions, categories, courses, course_instructors, course_delivery_options, course_sections, lessons, lesson_resources, physical_locations, course_batches, batch_schedules, enrollments, payments, payment_transactions, live_classes, assignments, assignment_submissions, quizzes, questions, quiz_options, quiz_attempts, quiz_answers, lesson_progress, attendance, reviews, certificates, notifications, announcements and audit_logs.
Review relationships before implementation. Document PKs, FKs, unique constraints, indexes, types, nullability and deletion behaviour.

SECURITY
Authentication: secure password hashing, email verification, password-reset protection, login throttling, secure sessions/cookies and optional administrator 2FA.
Authorisation: policies/gates, role permissions, ownership checks, server validation and IDOR prevention.
Application: CSRF, XSS, SQL injection prevention, safe output escaping, input validation, upload restrictions, MIME verification and safe filenames.
Infrastructure: production HTTPS, environment secrets, no committed .env, production debug off, least privilege, backups, secure errors and dependency updates.
Use OWASP Top 10 as a review checklist.

ADMIN PANEL
Use Filament customised to LearnBuddy. Include dashboard statistics, course/user/instructor management, enrolments, payments, video management, live scheduling, batches, attendance, assignments, quizzes, certificates, reports, audit logs and settings. Make it responsive and authorise every action server-side.

SOFTWARE ARCHITECTURE — MANDATORY
Use a modular monolithic Laravel architecture with clear separation of concerns. Follow the existing Laravel version and repository conventions; inspect before changing structure. Do not introduce microservices, a separate React/Node application, repositories, packages or abstraction layers without a documented technical need and owner approval.

Request lifecycle:
HTTP request → route → middleware (authentication/authorization/throttling) → Form Request validation → focused controller → business operation/service when justified → Eloquent model/database → Resource/consistent response or Blade view.

Layer responsibilities:
- Routes: declare endpoints, names and middleware only. No business logic or database queries.
- Controllers: coordinate HTTP input/output and call the appropriate operation. Keep them small; do not place complex workflows or SQL in controllers.
- Form Requests: validate and authorize incoming data at the boundary. Never rely only on browser-side validation.
- Services/actions: contain genuinely complex or reusable business workflows, especially multi-step operations. Do not create a service class for every trivial CRUD method.
- Eloquent models: define relationships, casts, scopes and model-level behaviour. Avoid turning models into oversized business-logic containers.
- Policies/Gates: enforce role, ownership and record-level permissions on the server.
- Events/listeners: use for decoupled side effects when this improves clarity; avoid unnecessary event chains.
- Jobs/queues: handle suitable slow or retryable work such as email, media processing and reminders. Make jobs safe to retry and idempotent where needed.
- Blade components: build reusable, focused UI elements; keep templates presentational and escape output safely.
- Configuration: centralise environment-dependent settings in config files; access environment variables through configuration, not throughout application code.

Suggested Laravel organisation (adapt to the installed Laravel version and existing code; do not force a restructure):
- app/Http/Controllers/{Admin,Instructor,Student,Public}
- app/Http/Requests/{...}
- app/Http/Middleware/
- app/Models/
- app/Policies/
- app/Services/ or app/Actions/ only for justified business workflows
- app/Jobs/, app/Events/, app/Listeners/, app/Notifications/
- app/Providers/
- app/Support/ or app/Enums/ for genuinely shared helpers/constants/enums
- resources/views/{layouts,components,public,student,instructor,admin}
- routes/{web.php,api.php} and feature-specific route files only if the project convention supports them
- database/{migrations,seeders,factories}
- tests/{Feature,Unit}
Do not create empty folders or speculative modules. Keep domain features cohesive and avoid circular dependencies.

SOFTWARE CODING STANDARDS — MANDATORY
Naming:
- PHP classes, controllers, models, requests, policies and components: PascalCase; use singular model names (Course, Enrollment).
- Methods and variables: camelCase; use descriptive names that express intent.
- Database tables: plural snake_case; columns: snake_case; foreign keys: singular_model_id (for example course_id).
- Routes: named consistently; URL segments use clear kebab-case where appropriate.
- Blade components: follow Laravel component naming conventions.
- Constants and enum cases: use clear, consistent names; avoid unexplained abbreviations.

PHP/Laravel:
- Follow PSR-12 and the installed Laravel version's conventions; use the repository's formatter/linter if configured.
- Use strict, appropriate type declarations and return types where practical.
- Use constructor or method dependency injection; avoid facades/global state when they make code harder to test, while respecting normal Laravel conventions.
- Prefer early returns and clear control flow over deeply nested conditionals.
- Keep methods focused and reasonably small; extract logic only when it improves clarity or reuse.
- Use Eloquent relationships and eager loading appropriately; prevent N+1 queries.
- Use pagination for potentially large result sets; do not load unlimited records by default.
- Use database transactions for multi-write workflows that must succeed or fail together. Consider locking/unique constraints for concurrent enrolments, batch capacity and payment activation.
- Use Laravel validation, authorization, password hashing, CSRF protection and output escaping correctly; do not hand-roll security mechanisms when framework features exist.
- Do not use raw SQL unless justified; always bind parameters. Never interpolate user input into SQL.
- Avoid magic numbers, duplicate code, unused imports, dead code, empty catch blocks, debugging dumps and commented-out code.
- Add comments for non-obvious business rules, not for code that is already self-explanatory. Document public APIs and complex workflows.

Blade/CSS/JavaScript:
- Use reusable Blade components for repeated interface patterns.
- Keep business/database logic out of Blade templates.
- Keep JavaScript focused on interaction; do not duplicate server-side business rules in the browser.
- Reuse the approved design tokens and existing UI components. Do not add arbitrary colours, spacing systems or competing component styles.
- Keep accessibility in mind: semantic HTML, labels, keyboard access, visible focus, sufficient contrast and appropriate touch targets.
- Mobile responsiveness remains mandatory for every page and every new component.

Database and data integrity:
- Every schema change must be represented by a migration; never silently edit an already-applied migration to change deployed schema.
- Define primary/foreign keys, unique constraints, indexes, nullability and deletion behaviour intentionally.
- Use database constraints as well as application validation for critical uniqueness and integrity rules.
- Use DECIMAL for monetary values; never use PHP/SQL floating-point arithmetic for money.
- Use soft deletes/archive status only where business/audit requirements justify them; do not use soft deletes indiscriminately.
- Seeders/factories must use safe sample data and must never contain real personal data or production secrets.

API and response standards (where APIs are used):
- Follow REST conventions and consistent status codes/JSON response shapes.
- Validate and authorize every request on the server.
- Use pagination, filtering and sorting for large collections.
- Do not expose internal exception details, SQL errors, secrets or stack traces to clients.
- Keep API contracts documented and stable; do not change response formats unexpectedly.

Testing and maintainability:
- Add/update tests with each feature, especially authorization, validation, business rules, edge cases and failure paths.
- Test both success and rejection paths; do not test only the happy path.
- Use factories and isolated test data where practical.
- Run the relevant test suite, formatter/linter and static checks that are actually configured.
- Do not claim checks passed unless they were executed and passed.
- Update relevant documentation and .env.example when configuration changes; include variable names and safe placeholders only.

CODE QUALITY
Follow Laravel conventions: MVC, focused controllers, Form Requests, service classes/actions only when justified, policies/gates, useful events/listeners, queued jobs, dependency injection, transactions, Eloquent relationships, reusable Blade components and meaningful naming. Avoid giant controllers, duplicated logic and unnecessary abstractions. The architecture and coding standards above apply to every phase and every file, not only project setup.

TESTING
Write unit/feature tests for registration/login, role permissions, publishing/discovery, free/paid enrolment, duplicate prevention, batch capacity, video authorisation, live access, submissions, quiz attempts, certificate eligibility, payment webhooks, notification delivery and Aiven connection. Test success, failure and security boundaries. Also manually check responsive layouts at all required viewports.

WORKING RULES
- Inspect existing files before modifying anything.
- Preserve reference UI and official logo.
- Mobile responsiveness is mandatory on every page.
- Use Aiven MySQL and require SSL.
- Never hardcode or commit secrets.
- Do not introduce paid services without approval.
- Do not skip validation, authorisation or tests.
- Do not create fake functionality or visual-only controls presented as working features.
- Explain important decisions in beginner-friendly language.
- After every phase report changed files, implementation details, database changes, tests run, manual test instructions and known limitations.
- Ask questions only when a decision blocks implementation.
- Never claim completion when a feature is only a mockup.
- Implement only the phase requested. Do not start later phases, add speculative features or refactor unrelated code.
- Do not overwrite existing work without inspecting it and explaining the reason.
- Do not make destructive database changes without explicit approval and a backup.
- Stop at the end of each phase and wait for the owner's manual test and approval.

GIT, REVIEW AND PHASE GATE — MANDATORY
- Inspect `git status`, current branch and recent history before starting a phase. Preserve all pre-existing uncommitted owner changes; never overwrite or discard them.
- Prefer a dedicated feature branch for substantial work (for example `feature/course-enrolment`) and keep `main` stable. Follow the repository's existing branching strategy if one is already established.
- Before implementation, establish a clean checkpoint. Do not blindly run `git add .` because it may stage secrets, generated files or unrelated work. Review `.gitignore`, use `git status --short`, stage only intended files and inspect `git diff --cached` before committing.
- Use clear Conventional Commit messages, for example `feat(enrolment): add free course enrolment`, `fix(auth): enforce instructor ownership`, `test(course): cover publication rules`, `docs(setup): update Aiven SSL instructions`.
- Keep each commit to one logical feature/fix. Include its migrations, tests and documentation when applicable. Never commit `.env`, credentials, API keys, private certificates, private uploads, build artefacts or unrelated files.
- Before suggesting a commit, run applicable tests and checks, `git diff --check`, inspect `git diff` and verify the staged diff contains only intended changes. Report commands and actual results.
- After implementing a phase, provide: summary; files created/modified/deleted; architecture decisions; database/migration impact; routes and permissions changed; tests/checks actually run; exact owner manual-test steps with expected results; known limitations; and one suggested commit message.
- The owner manually tests each phase without AI. Do not claim to have performed or certified the owner's manual browser/device test. Provide exact steps and expected results.
- STOP after each phase. Do not start the next phase, refactor unrelated code or perform extra features. Wait for the owner's explicit approval after manual testing and commit.
- If a test fails, stay in the current phase, identify the root cause, make the smallest safe correction and repeat relevant checks. Do not hide failing tests or mark the phase complete prematurely.
- Never run destructive database commands or production migrations without explicit approval, a reviewed migration plan and a verified backup/rollback path.
```

------------------------------------------------------------------------

# PROMPT 1 --- INSPECTION ONLY (NO CODE CHANGES)

``` text
We are starting LearnBuddy. Follow the project rules in PROMPT 0.

This phase is INSPECTION ONLY. Do not create, edit, delete, move, install or reformat any files. Do not run migrations or change the database.

Inspect:
1. Entire existing project structure.
2. `refrence_ui/` — all relevant HTML, CSS, JavaScript and assets.
3. `logo/` — identify official logo files and variants.
4. Existing Laravel/PHP setup and version.
5. Composer dependencies and frontend dependencies already installed.
6. Existing routes, controllers, models, migrations, views and configuration.
7. Current database configuration, without printing or exposing secrets.
8. Existing Git status and `.gitignore` (do not commit anything).
9. Existing responsive patterns and any current tests.

Report:
- What exists and what is missing.
- Reference UI design observations: typography, layout, spacing, colours, navigation, cards, buttons and interactions.
- Logo files found and recommended usage.
- Existing technologies/dependencies.
- Desktop and mobile design requirements.
- Proposed Laravel architecture.
- Initial database relationship plan.
- Aiven connection and SSL configuration plan.
- Sri Lanka-specific requirements and decisions needing owner approval.
- Security risks and questions.
- Ordered implementation phases.

Do not claim to have inspected inaccessible files. Do not begin implementation. Wait for my approval.
```

**Owner gate:** Review the report and approve the architecture before
Prompt 2.

------------------------------------------------------------------------

# PROMPT 2 --- ARCHITECTURE AND DATABASE DESIGN

``` text
Implement only the architecture/design preparation for LearnBuddy, based on the approved inspection report and PROMPT 0.

Do not build user-facing features yet. Do not run destructive commands or production migrations.

Tasks:
1. Finalise the Laravel folder/module approach using conventional Laravel architecture.
2. Design the relational ER model for all planned entities:
   users, roles, permissions, categories, courses, course_instructors, course_delivery_options, course_sections, lessons, lesson_resources, physical_locations, course_batches, batch_schedules, enrollments, payments, payment_transactions, live_classes, assignments, assignment_submissions, quizzes, questions, quiz_options, quiz_attempts, quiz_answers, lesson_progress, attendance, reviews, certificates, notifications, announcements and audit_logs.
3. Define primary keys, foreign keys, data types, nullability, indexes, unique constraints and deletion/archive behaviour.
4. Define relationships and cardinalities.
5. Plan role/permission boundaries for administrator, instructor and student.
6. Model a course once with multiple delivery options (Online, Physical or Both), each with independent pricing, capacity, schedule, location, batch and access rules.
7. Plan safe enrolment and batch-capacity concurrency handling.
8. Plan money storage using DECIMAL and currency codes, with LKR as Sri Lanka's default. Do not hardcode tax rates.
9. Plan Sri Lankan province/district/address fields and +94 phone handling without over-restricting valid data.
10. Use Asia/Colombo for user-facing scheduling.
11. Plan future English/Sinhala/Tamil localisation.
12. Plan provider-independent payment, video, live-class, storage, email and notification interfaces.
13. Plan privacy, audit, retention and access controls, flagging legal decisions for owner/professional review.

Deliver architecture notes and an ER diagram/document in the project only if that fits existing project conventions. Do not create application functionality or migrations in this phase unless specifically approved.

Report every file created/changed and why. Give me a review checklist. Stop and wait.
```

**Owner gate:** Check the ER diagram, especially courses, delivery
options, batches, enrolments, payments and user roles.

------------------------------------------------------------------------

# PROMPT 3 --- LARAVEL BASE AND AIVEN MYSQL SSL

``` text
Implement only the project foundation and database connectivity for LearnBuddy.

Before editing, inspect the current state and preserve existing work.

Tasks:
1. Confirm Laravel/PHP versions and compatibility.
2. Configure the existing Laravel project without replacing its architecture.
3. Configure Tailwind CSS and the existing frontend build process.
4. Configure MySQL using the approved Aiven settings:
   DB_CONNECTION=mysql
   DB_HOST=mysql-3e258d69-fwvhaqc2992786-b39c.k.aivencloud.com
   DB_PORT=23001
   DB_DATABASE=defaultdb
   DB_USERNAME=avnadmin
   DB_PASSWORD from a local environment secret only.
5. Configure the Aiven CA certificate path and PDO SSL CA option correctly in Laravel.
6. Require encrypted SSL. Never disable certificate verification or fall back to an unencrypted connection.
7. Add/verify `.env` is ignored by Git. Update `.env.example` with placeholders only.
8. Never print secrets in terminal output, logs, exception pages or reports.
9. Add a safe database health check that reveals connection success/failure without revealing credentials or infrastructure secrets.
10. Document local Windows setup using Laragon or XAMPP, as appropriate to the existing project.
11. Keep development and production environment settings separate.
12. Do not run destructive migrations.

Test the Laravel-to-Aiven connection if the required local credentials and CA certificate are already configured. If they are not available, explain exactly what I must configure locally; do not ask me to paste secrets into chat.

Report versions, files changed, SSL configuration, connection test result and manual verification steps. Stop.
```

**Owner gate:** Verify the connection from your own environment and
confirm SSL. Do not continue if the database connection is not secure
and working.

------------------------------------------------------------------------

# PROMPT 4 --- DESIGN SYSTEM AND RESPONSIVE SHELL

``` text
Implement only the LearnBuddy design system and reusable application shell.

Use the exact approved `refrence_ui/` design and official logo in `logo/`. Do not invent a different visual identity.

Tasks:
1. Create reusable design tokens using the exact brand palette.
2. Establish typography, spacing, sizing, borders, shadows, buttons, form controls, cards, badges, alerts and modal styles.
3. Create reusable Blade components where appropriate.
4. Build the shared public layout, header, footer and navigation.
5. Implement functional desktop and mobile navigation, including touch-friendly dropdowns and open/close behaviour.
6. Add accessible focus states, semantic structure and sufficient contrast.
7. Keep animations purposeful and restrained.
8. Do not create fake links or controls. Any visible control must work or be clearly marked as not yet available.
9. Ensure layouts adapt without horizontal overflow.
10. Use responsive Tailwind utilities and avoid duplicated breakpoint CSS.

Manually check the shell at 360×800, 390×844, 430×932, 768×1024, 1024×768, 1366×768 and 1920×1080.

Do not implement authentication, course features or dashboards in this phase. Report changed files and provide a manual visual test checklist. Stop.
```

**Owner gate:** Compare the result against the reference UI on desktop
and mobile.

------------------------------------------------------------------------

# PROMPT 5 --- AUTHENTICATION AND ROLE AUTHORISATION

``` text
Implement only authentication and role/permission foundations.

Roles: Administrator, Instructor and Student.

Tasks:
1. Implement registration, login, logout, email verification and password recovery using an appropriate Laravel authentication solution.
2. Secure password hashing, reset-token handling, login throttling, secure sessions and cookies.
3. Validate Sri Lankan phone numbers including +94 sensibly, without making phone the only login identifier unless approved.
4. Use server-side role/permission checks, Laravel Policies and Gates.
5. Apply least privilege. A user must not access another user's private data by changing an ID in a URL.
6. Provide optional administrator two-factor authentication only if the chosen supported package is approved and configured safely.
7. Add safe validation errors and avoid revealing whether sensitive accounts exist.
8. Keep the UI consistent with LearnBuddy and responsive at all required sizes.
9. Add feature tests for registration, login, logout, password reset, verification, throttling and role boundaries.
10. Do not yet implement course management or dashboards.

Create only the necessary migrations/models/policies/routes/views. Report all changes and tests. Give me manual test accounts/setup instructions that do not use real user data. Stop.
```

**Owner gate:** Manually test each role and attempt to access routes
belonging to another role.

------------------------------------------------------------------------

# PROMPT 6 --- COURSE MANAGEMENT (ADMIN AND INSTRUCTOR)

``` text
Implement only course management for authorised administrators and instructors.

Course information:
- Title and unique slug.
- Description and thumbnail.
- Category.
- One or more authorised instructors.
- Learning outcomes and prerequisites.
- Difficulty level and language.
- Duration.
- Pricing.
- Delivery modes.
- Curriculum.
- Publication status.

Lifecycle: Draft → Review → Published → Archived.

Tasks:
1. Build secure CRUD for courses and categories.
2. Implement course-instructor relationships.
3. Ensure instructors can manage only courses they are authorised to manage.
4. Administrators can manage all courses.
5. Model one course with Online, Physical or Both delivery options; do not create duplicate course records for delivery modes.
6. Each delivery option can have its own price, availability, capacity, schedule, location, batch and access conditions.
7. Validate all input server-side using Form Requests.
8. Use policies for every create, view, update, publish, archive and delete action.
9. Use safe image upload validation, MIME checks, size limits and safe filenames.
10. Do not expose unpublished courses publicly.
11. Make all management screens responsive.
12. Add feature tests for CRUD, instructor ownership, publishing lifecycle and unauthorised access.

Do not implement public catalogue or student enrolment yet. Report changes, tests and manual checks. Stop.
```

**Owner gate:** Test administrator and instructor access separately,
including direct URL access.

------------------------------------------------------------------------

# PROMPT 7 --- PUBLIC HOMEPAGE, CATALOGUE AND COURSE DETAILS (DONE)

``` text
Implement only the public LearnBuddy discovery experience.

Pages:
- Homepage.
- Course catalogue.
- Search and filters.
- Course details.
- Instructor profiles.
- Published course reviews/ratings where valid.
- Registration/login entry points.

Tasks:
1. Follow `refrence_ui/` and the LearnBuddy brand.
2. Display only published courses.
3. Add working search and useful filters such as category, delivery mode, language, difficulty and price where supported by actual data.
4. Show course title, thumbnail, instructor, description, delivery options, duration, language, price and rating only when data exists.
5. Show LKR as the default Sri Lankan currency format. Keep currency formatting reusable and do not hardcode amounts.
6. Clearly distinguish Online, Physical and hybrid availability.
7. Display Sri Lankan local information such as physical location and schedule only when configured.
8. Do not invent course content, instructors, reviews, prices or ratings.
9. Add pagination and sensible empty/loading/error states.
10. Make search, filters, cards and course details mobile-friendly.
11. Add tests for public visibility, search/filter behaviour and unpublished course protection.

Do not implement enrolment or payment in this phase. Report changes and manual test steps. Stop.
```

**Owner gate:** Test search, filters, empty results, course details and
mobile navigation.

------------------------------------------------------------------------

# PROMPT 8 --- ENROLMENT AND STUDENT DASHBOARD (DONE)

``` text
Implement only course enrolment and the initial student dashboard.

Enrolment flow:
1. Student selects a course and views details.
2. Student chooses Online or Physical.
3. If Physical, student selects an available batch.
4. Server rechecks current price, delivery availability and remaining capacity.
5. Student confirms.
6. Free enrolment is created only after server-side validation.
7. Paid enrolment must remain pending until a payment is verified in the later payment phase.
8. Successful enrolment appears in the student dashboard and a confirmation is generated.

Tasks:
- Prevent duplicate enrolments with database constraints and safe transaction handling.
- Prevent physical batch overbooking, including concurrent requests.
- Do not trust price, capacity, delivery mode or user ID submitted by the browser.
- Ensure a student can view only their own enrolments.
- Build dashboard sections for enrolled courses, continue learning, upcoming live classes, physical schedule, pending assignments, quiz activity, progress, certificates, notifications and recent activity. For features not implemented yet, show honest empty/not-yet-available states rather than fake data.
- Make the dashboard fully responsive.
- Add tests for free enrolment, paid pending state, duplicate prevention, capacity, race-condition safeguards and ownership/IDOR boundaries.

Do not implement payment processing or learning content in this phase. Report changes and manual tests. Stop.
```

**Owner gate:** Test free enrolment, paid pending state, duplicate
enrolment and full-batch rejection.

------------------------------------------------------------------------

# PROMPT 9 --- LEARNING INTERFACE AND LESSON PROGRESS (DONE)

``` text
Implement only the student learning interface and lesson progress.

Tasks:
1. Build a responsive learning screen with course curriculum, sections and lessons.
2. Allow access only to students with a valid active enrolment and instructors/admins with appropriate permissions.
3. Provide lesson navigation and clear current-lesson state.
4. Support lesson completion tracking and progress calculation.
5. Provide notes and lesson resources where the data model supports them.
6. Do not expose private or unpublished lessons to unauthorised users.
7. Ensure progress updates are validated server-side and cannot be manipulated to claim completion of inaccessible lessons.
8. Keep video area responsive, but do not implement the external video provider in this phase.
9. Provide assignments, quizzes and discussions as honest navigation/placeholders until their phases are implemented.
10. Test access control, lesson navigation, progress persistence, invalid lesson IDs and IDOR protection.
11. Manually check mobile video-area sizing, curriculum navigation and touch interactions.

Do not implement video hosting, assignments, quizzes or certificates in this phase. Report changes and stop.
```

**Owner gate:** Test as enrolled student, non-enrolled student,
instructor and administrator.

------------------------------------------------------------------------

# PROMPT 10 --- VIDEO INTEGRATION (DONE)

``` text
Implement only the recorded-video integration for LearnBuddy.

Evaluate Mux and verify current pricing, free-tier limits, required account setup and technical requirements before implementation. Do not assume it is free or unlimited. Obtain owner approval before using a paid service.

Requirements:
- Secure direct uploads for authorised instructors/admins.
- Private video assets.
- Provider webhooks with signature verification.
- Processing status and failure handling.
- Signed playback access and short-lived tokens.
- Enrolment and lesson-level authorisation on every playback request.
- No permanent private tokens or secrets in frontend code.
- No public storage for private videos.
- HLS playback where supported.
- Watermarking where appropriate.
- Safe handling of provider outages and processing delays.
- Prevent IDOR and unauthorised playback.
- Do not claim screen recording can be completely prevented.

Use a provider interface so the video provider can be replaced later. Add tests for upload permissions, webhook validation, playback authorisation, expired tokens and unauthorised lesson access.

If provider credentials are not available, implement only an appropriately abstracted integration boundary and clearly state what remains unconfigured. Do not fabricate successful uploads or playback. Report costs/limits verified, files changed, tests and manual checks. Stop.
```

**Owner gate:** Test authorised and unauthorised playback, token expiry
and mobile playback.

------------------------------------------------------------------------

# PROMPT 11 --- ASSIGNMENTS AND QUIZZES

``` text
Implement only assignments, submissions, quizzes and quiz attempts.

ASSIGNMENTS
- Instructors create/manage assignments only for authorised courses.
- Students submit only for courses in which they have valid enrolment.
- Support instructions, due dates and permitted attachments.
- Validate file type, MIME, size and safe filenames.
- Prevent students from accessing another student's submission.
- Allow authorised instructors to review submissions and provide feedback/grades.

QUIZZES
- Instructors create quizzes and questions for authorised courses.
- Support questions, options, correct-answer configuration, marks and attempt rules as approved.
- Students can attempt only quizzes they are authorised to access.
- Validate attempt limits and availability on the server.
- Calculate results securely server-side.
- Do not expose correct answers before the appropriate release time.
- Prevent duplicate or invalid submissions and handle repeated requests safely.

Make all screens responsive. Add tests for ownership, due dates, file validation, attempt limits, scoring, answer visibility and IDOR. Do not implement certificates in this phase. Report changes and manual tests. Stop.
```

**Owner gate:** Submit an assignment and attempt a quiz using separate
student and instructor accounts.

------------------------------------------------------------------------

# PROMPT 12 --- PHYSICAL BATCHES, LOCATIONS AND ATTENDANCE

``` text
Implement only physical learning operations.

Tasks:
1. Admins manage Sri Lankan physical locations using structured province, district, city/town and address fields, without assuming all addresses are identical.
2. Admins and authorised instructors manage physical course batches.
3. Each batch has capacity, schedule, location, status and associated course delivery option.
4. Prevent enrolment beyond capacity, including concurrent enrolment requests.
5. Support schedule changes and cancellations with appropriate records/notifications hooks.
6. Instructors record attendance only for authorised courses/batches and scheduled sessions.
7. Students can view their own batch schedule and attendance.
8. Apply Asia/Colombo timezone consistently.
9. Use safe date/time validation and display unambiguous local times.
10. Keep all views responsive, including tables on mobile.
11. Add tests for capacity, schedule, instructor ownership, attendance creation and student privacy.

Do not implement live video classes or notifications delivery in this phase. Report changes and manual test steps. Stop.
```

**Owner gate:** Test a physical enrolment, full batch, schedule display
and attendance privacy.

------------------------------------------------------------------------

# PROMPT 13 --- LIVE CLASSES

``` text
Implement only live-class scheduling and joining.

Evaluate Jitsi Meet for the initial implementation. Verify current hosting options, usage limitations, privacy/security considerations and any costs before choosing a deployment approach. Do not assume a hosted service is free or suitable for commercial use without checking.

Each live session must include:
- Course.
- Instructor.
- Title and description.
- Start/end time.
- Meeting provider.
- Meeting identifier.
- Status.
- Access permissions.

Support scheduling, rescheduling, cancellation, upcoming/completed sessions, reminders hooks and instructor access.

Rules:
- Only authorised enrolled students may join.
- Instructors may manage sessions only for courses they control.
- Administrators may manage all sessions.
- Never expose private meeting credentials or administrative meeting controls to students.
- Use Asia/Colombo for scheduling and display.
- Handle cancelled, completed and not-yet-started sessions.
- Joining must work on supported mobile browsers.

Add tests for authorisation, session lifecycle, time boundaries and unauthorised access. If the provider is not configured, show a clear unconfigured state instead of a fake working meeting. Report current provider limits/costs checked, changed files and manual tests. Stop.
```

**Owner gate:** Test joining as an enrolled student and rejection as a
non-enrolled student.

------------------------------------------------------------------------

# PROMPT 14 --- PAYMENTS (SRI LANKA-FIRST)

``` text
Implement only the payment architecture and approved payment provider integration.

First research current payment gateways that can onboard eligible Sri Lankan businesses. Consider locally available providers such as PayHere or WEBXPAY only as candidates to verify, not as assumed choices. Check:
- Sri Lankan merchant eligibility and documentation.
- Supported currencies and LKR settlement.
- Fees and transaction charges.
- Refund and chargeback support.
- Sandbox availability.
- API and webhook documentation.
- Recurring payment support only if required.
Present findings and ask for approval of the provider before integrating it. Do not introduce a paid provider without approval.

Build a provider-independent architecture for:
- Orders.
- Course prices.
- Currency (LKR default).
- Transaction references.
- Payment status.
- Payment history.
- Receipts.
- Refund status.
- Failed payments.

Security:
- Never store card numbers or CVV.
- Verify payment status server-side.
- Validate webhook signatures.
- Use idempotency.
- Use database transactions.
- Prevent duplicate enrolment activation.
- Keep audit records.
- Do not trust browser redirect success as proof of payment.
- Use sandbox mode until production credentials are configured.
- Keep credentials in environment secrets only.

Ensure paid enrolment activates only after verified successful payment. Handle pending, failed, cancelled, expired and refunded states. Add tests for webhook signatures, replay/duplicate events, amount/currency mismatch, order ownership and activation. Report provider research and implementation boundaries. Stop.
```

**Owner gate:** Test successful, failed, cancelled and repeated webhook
scenarios in sandbox. Never test with real money without explicit
approval.

------------------------------------------------------------------------

# PROMPT 15 --- NOTIFICATIONS AND ANNOUNCEMENTS

``` text
Implement only notifications and course announcements.

Use Laravel Notifications and queued jobs.

Support:
- Registration verification.
- Password resets.
- Enrolment confirmation.
- Payment confirmation.
- Live-class reminders.
- Rescheduling and cancellations.
- Assignment notifications.
- Quiz results.
- Certificates.
- Course announcements.

Requirements:
1. Add in-app read/unread status.
2. Avoid duplicate reminders and duplicate event notifications.
3. Respect user preferences and notification permissions.
4. Use Asia/Colombo for scheduled reminders.
5. Queue delivery and handle retries/failures safely.
6. Evaluate Resend for transactional email and Firebase Cloud Messaging for push notifications. Verify current free-tier limits, pricing and requirements. Do not assume unlimited free usage or configure paid services without approval.
7. Keep provider integrations replaceable.
8. Never expose another user's notifications.
9. Add tests for ownership, read/unread, duplicate prevention, queued delivery and failure handling.
10. Make notification screens responsive.

If provider credentials are missing, keep the integration clearly unconfigured and do not claim messages were delivered. Report changed files, provider status, tests and manual steps. Stop.
```

**Owner gate:** Test in-app notifications, read/unread and duplicate
prevention.

------------------------------------------------------------------------

# PROMPT 16 --- CERTIFICATES AND REVIEWS

``` text
Implement only certificate eligibility/issuance and course reviews/ratings.

CERTIFICATES
- Define eligibility based on the approved course completion rules.
- Verify required lessons, assignments and quizzes server-side.
- Issue certificates only to eligible students.
- Use unique, non-guessable certificate identifiers.
- Provide a verification page that reveals only intended certificate information.
- Prevent unauthorised issuance, modification and duplicate issuance.
- Preserve issuance/audit records.

REVIEWS AND RATINGS
- Allow reviews only from eligible enrolled students according to the approved policy.
- Validate rating range and review content.
- Prevent unauthorised edits and duplicate reviews according to the chosen policy.
- Allow administrators to moderate inappropriate content.
- Do not fabricate ratings or testimonials.

Add tests for eligibility, duplicate issuance, verification privacy, review ownership and moderation. Ensure responsive layouts. Report changes and manual tests. Stop.
```

**Owner gate:** Test eligible/ineligible certificate issuance and review
ownership.

------------------------------------------------------------------------

# PROMPT 17 --- FILAMENT ADMINISTRATION

``` text
Implement the LearnBuddy administration panel using Filament, customised to the existing brand and responsive design.

Include:
- Dashboard statistics.
- Course management.
- User management.
- Instructor management.
- Enrolments.
- Payments.
- Video management.
- Live-class scheduling.
- Batch management.
- Attendance.
- Assignments.
- Quizzes.
- Certificates.
- Reports.
- Audit logs.
- System settings.

Requirements:
1. Inspect the installed Filament version and use compatible APIs.
2. Apply the administrator/instructor permission model correctly.
3. Instructors must see/manage only authorised resources.
4. Every administrative action must be authorised server-side.
5. Protect sensitive payment, user and audit information.
6. Add confirmation for destructive or high-impact actions.
7. Keep all resources usable on mobile and tablet.
8. Avoid exposing secrets or provider credentials in the UI.
9. Add tests for resource access and critical administrative actions.

Do not rebuild completed features unnecessarily. Report all resources, permissions, responsive checks and tests. Stop.
```

**Owner gate:** Test admin and instructor access separately, including
direct URLs and destructive-action confirmation.

------------------------------------------------------------------------

# PROMPT 18 --- REPORTING, AUDIT AND SYSTEM SETTINGS

``` text
Implement only reporting, audit-log review and configurable system settings not already completed.

Reports should use actual database data, with clear date ranges and filters. Include appropriate course, enrolment, payment, attendance and learning-progress reporting based on the implemented data model.

Requirements:
- Never show invented statistics.
- Restrict reports to the user's permissions.
- Protect personal and payment-related information.
- Keep audit logs tamper-resistant from ordinary users and record important administrative/security actions.
- Avoid logging passwords, tokens, card details or secrets.
- Make configurable settings for currency display, timezone, course defaults, notification preferences and approved provider settings where appropriate.
- LKR and Asia/Colombo remain the Sri Lanka defaults.
- Do not hardcode tax/legal values; keep approved values configurable.
- Make reports responsive and accessible.
- Add tests for permissions, date filters, data accuracy and sensitive-data redaction.

Report changed files, settings introduced, test results and manual checks. Stop.
```

**Owner gate:** Compare report totals with the underlying records and
test role restrictions.

------------------------------------------------------------------------

# PROMPT 19 --- FULL QUALITY, SECURITY AND RESPONSIVE AUDIT

``` text
Perform a review and remediation phase for the existing LearnBuddy implementation. Do not add unrelated features or redesign the approved UI.

SECURITY REVIEW
Use OWASP Top 10 as a checklist. Inspect:
- Authentication and session security.
- Authorisation and role boundaries.
- IDOR and ownership checks.
- CSRF, XSS and SQL injection protections.
- Input validation and output escaping.
- File upload/MIME/size/path safety.
- Payment verification, webhook signatures and idempotency.
- Video access and token expiry.
- Live-class access.
- Secrets, environment configuration and error messages.
- Dependency vulnerabilities.
- Audit logging and privacy exposure.
- Database constraints and transaction/concurrency safety.

TEST REVIEW
Run the available automated unit and feature tests for:
- Registration/login.
- Role permissions.
- Course publishing/discovery.
- Free and paid enrolment.
- Duplicate enrolment prevention.
- Batch capacity.
- Video playback authorisation.
- Live-class access.
- Assignment submissions.
- Quiz attempts.
- Certificate eligibility.
- Payment webhooks.
- Notification delivery.
- Aiven database connection.

Test success, failure and security boundaries. Do not hide or skip failing tests. Do not claim a test passed if it was not run.

RESPONSIVE REVIEW
Check all public, student, instructor, admin and learning pages at:
360×800, 390×844, 430×932, 768×1024, 1024×768, 1366×768 and 1920×1080.
Check overflow, broken layouts, overlaps, text wrapping, navigation, touch targets, forms, modals, tables, video, loading and empty states.

PERFORMANCE AND QUALITY
Review database query efficiency, N+1 queries, pagination, indexes, caching opportunities, asset loading, accessibility, maintainability and error handling.

Report:
1. Issues found, classified by severity.
2. Files changed and exact fixes.
3. Tests actually run and results.
4. Issues requiring owner action.
5. Manual test checklist.
6. Remaining known limitations.

Do not deploy, alter production data or perform destructive operations. Stop.
```

**Owner gate:** You manually run the full regression checklist. Fix all
critical/high-severity issues before deployment.

------------------------------------------------------------------------

# PROMPT 20 --- DEPLOYMENT READINESS (NO AUTOMATIC PRODUCTION DEPLOY)

``` text
Prepare LearnBuddy for deployment, but do not deploy or change production infrastructure without my explicit approval.

Tasks:
1. Document a production deployment plan suitable for the selected hosting environment.
2. Document required PHP extensions, PHP version, Composer dependencies, web server configuration and frontend asset build.
3. Configure production environment guidance: APP_ENV=production, APP_DEBUG=false, HTTPS, secure cookies and secret management.
4. Configure Aiven MySQL SSL CA correctly for production.
5. Document safe migration and rollback procedures, including backup requirements.
6. Document queue workers, scheduler/cron, storage permissions and public/private file handling.
7. Document storage, video, live-class, email, push and payment provider credentials as environment-variable names only.
8. Ensure `.env`, credentials, CA private material and secrets are not committed.
9. Provide backup, restore, monitoring, logging and incident-response guidance.
10. Provide a deployment smoke-test checklist.
11. Document Sri Lanka defaults: LKR, Asia/Colombo, +94-aware phone handling and address/localisation approach.
12. List all recurring costs, provider limits and services that require paid plans based on verified information. Mark anything not verified.
13. Do not claim legal compliance certification. List privacy/legal questions for professional review.

Do not deploy, expose credentials, run destructive migrations or modify production data. Provide the final deployment runbook and wait for approval.
```

------------------------------------------------------------------------

# MANUAL TEST AND COMMIT TEMPLATE --- USE AFTER EVERY PHASE

After Antigravity completes a phase, use this checklist yourself.

``` text
PHASE:
[Write phase number and name]

MANUAL TESTS
[ ] Application starts without errors.
[ ] Relevant page/feature loads.
[ ] Main success path works.
[ ] Invalid input is rejected correctly.
[ ] Unauthorised access is rejected.
[ ] Data persists correctly in MySQL where applicable.
[ ] No unrelated feature has regressed.
[ ] Desktop layout checked.
[ ] Mobile layout checked at relevant viewport sizes.
[ ] No horizontal overflow.
[ ] Browser console checked.
[ ] Git diff reviewed.
[ ] No secrets or .env files staged.

RESULT:
PASS / FAIL

NOTES:
[Write actual observations and errors]

Only commit when the phase passes.
```

Suggested commit naming:

``` text
feat: add responsive design system
feat: implement authentication and roles
feat: implement course management
feat: add public course catalogue
feat: implement enrolment workflow
feat: add student learning interface
feat: integrate secure video playback
feat: implement assignments and quizzes
feat: add physical batch management
feat: implement live classes
feat: integrate approved payment gateway
feat: add notifications
feat: implement certificates and reviews
feat: add Filament administration
test: complete security and responsive audit
docs: add deployment runbook
```

## Important project decisions to confirm before implementation

-   Which hosting provider will host the Laravel application? Aiven is
    the database host, not the Laravel application host.
-   Is the initial platform English-only, with Sinhala and Tamil planned
    for a later release?
-   Which Sri Lankan payment provider will be approved after eligibility
    and fees are checked?
-   Will live classes use a managed Jitsi service or a self-hosted
    deployment?
-   Which video/storage services are approved after free-tier and
    pricing checks?

## Final development principle

**Prompt → Implement → Review → Commit → Manually test → Approve → Next
prompt.**

Do not let Antigravity build the entire LMS in one run. Keep every phase
small enough that you can understand, test and confidently approve it.
