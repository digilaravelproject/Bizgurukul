# Implementation Plan: Career Panel (Job Board) Module

## Objective

Build a comprehensive Career Panel module allowing admins to manage job listings with rich text, dynamic filters, and active/inactive status, while logged-in users can browse, search, filter, and apply for active jobs with a seamless AJAX/Alpine-driven UI. The backend will strictly adhere to the Repository-Service-Controller pattern, use Form Requests for validation, and employ DB transactions with proper error handling.

do not use job model or databse table name use onther one llike careerjob

## Key Files & Context

- Stack: Laravel, Blade, Alpine.js, Tailwind CSS.
- **Migrations & Models**:
    - `JobTitle`, `JobLocation`, `JobExperience`, `JobSalary`, `JobSkill` (Master Models)
    - `Job` (Main Model, now includes `is_active` boolean)
    - `job_job_skill` (Pivot table)
- **Architecture Pattern**:
    - `JobRepository` (Data access & DB locks)
    - `JobService` (Business logic, transactions, try-catch)
    - `Admin\JobController` & `JobController` (HTTP request handling)
    - `StoreJobRequest` & `UpdateJobRequest` (Validation)
- **Views**:
    - `admin.jobs.create`, `admin.jobs.index`, `admin.jobs.edit` (Admin panel)
    - `jobs.index`, `jobs.show`, `jobs._list` (User panel)
- **Seeder**: `JobMasterSeeder`

## Implementation Steps

### Phase 1: Database & Models

1. Create migrations for `job_titles`, `job_locations`, `job_experiences`, `job_salaries`, and `job_skills`.
2. Create migration for `jobs` table:
    - `company_name` (string)
    - `company_logo` (string/nullable)
    - `job_title_id` (foreign key)
    - `job_location_id` (foreign key)
    - `job_experience_id` (foreign key)
    - `job_salary_id` (foreign key, nullable)
    - `description` (longtext)
    - `apply_link` (string)
    - `posted_on` (date)
    - `is_active` (boolean, default true)
3. Create pivot table migration `job_job_skill` (`job_id`, `job_skill_id`).
4. Generate Eloquent models. In the `Job` model, add a local scope `scopeActive($query)` to easily filter active jobs for users.

### Phase 2: Database Seeder

1. Create `JobMasterSeeder` to insert the specific preset data for Titles, Experiences, Locations, Salaries, and Skills.
2. Ensure "Remote" is seeded in Locations and "undisclosed" logic is supported via nullable `job_salary_id`.

### Phase 3: Architecture & Admin Panel (Job Management)

1. **Form Requests**: Create `StoreJobRequest` and `UpdateJobRequest` to strictly validate all incoming data.
2. **Repository**: Create `JobRepository` to handle DB interactions (e.g., `create`, `update`, `findByIdWithLock` using `lockForUpdate()`).
3. **Service**: Create `JobService`. Methods like `createJob` and `updateJob` will:
    - Wrap logic in `DB::beginTransaction()` and `DB::commit()`.
    - Include `try-catch` blocks. In the `catch`, log the error and trigger `DB::rollBack()`, then throw a custom exception or return an error response.
    - Handle file uploads (company logo).
    - Sync skills in the pivot table.
4. **Controller**: Implement `Admin\JobController` that depends on `JobService`. It handles views and redirects based on Service outcomes.
5. **Views**: Build `admin.jobs.create` and `admin.jobs.index` views.
    - Dropdowns for master data.
    - Multi-select for Skills.
    - Toggle switch/checkbox for `is_active` status.
    - Rich Text Editor (Quill/Trix) for `description`.

### Phase 4: User Side - Career Panel & Filters

1. Restrict routes using the `auth` middleware for jobs listing.
2. Implement `JobController@index` using the Service/Repository to fetch only _active_ jobs (`is_active = true`), supporting AJAX filtering and searching.
3. Build the UI in Blade (`jobs.index`):
    - Sidebar for filters (Location, Salary, Experience, Skills).
    - Global Search input.
    - Alpine.js `x-data` with debounced search (`x-model.debounce.500ms`) to trigger fetch requests and dynamically update the job list without full page reloads.

### Phase 5: Job Details & Apply

1. Implement `JobController@show`, ensuring the job is active.
2. Build `jobs.show` Blade view:
    - Render `{!! $job->description !!}` safely.
    - Display logo, posted date, tags (skills).
    - Include "Apply Now" button (`target="_blank"`).

## Verification & Testing

- Run migrations and `JobMasterSeeder`. Check DB integrity.
- Test Admin flow: Create, Edit, Toggle Status using FormRequests and Service layer. Ensure DB transactions rollback correctly on forced errors.
- Test User flow: Only active jobs are visible. Test search debouncing, correct filter application via AJAX.
- Verify "Apply Now" external link behavior.
