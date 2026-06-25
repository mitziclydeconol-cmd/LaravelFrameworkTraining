# CodeTrack AI 🚀

**An AI-Assisted Coding Progress Tracker for IT Students**

Built with Laravel 11, MySQL, Blade, Bootstrap 5, and optional Claude AI integration.

---

## ✨ Features

### Student Module
- **Dashboard** — Total hours, activity streak, weekly chart, language breakdown, subject progress
- **Coding Logs** — Full CRUD with title, description, language, time, date, difficulty, code snippet
- **AI Feedback** — One-click code review via Claude (Anthropic API)

### Instructor Module
- **Analytics Dashboard** — System-wide charts, top students, subject engagement
- **Student Management** — Browse, search, filter, view individual progress
- **CSV Export** — Export all logs or per-student reports

### Subjects Module
- Create, edit, delete subjects with color coding
- Assign/remove students from subjects
- View subject-level analytics

### Auth
- Laravel Breeze authentication
- Student & Instructor roles
- Role-based middleware protection

---

## 🗂️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── RegisteredUserController.php  ← role-aware registration
│   │   ├── Instructor/
│   │   │   ├── DashboardController.php
│   │   │   └── StudentController.php         ← includes CSV export
│   │   ├── Student/
│   │   │   ├── DashboardController.php
│   │   │   └── CodingLogController.php       ← CRUD + AI feedback
│   │   └── SubjectController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php                ← role:student / role:instructor
│   └── Requests/
│       ├── StoreCodingLogRequest.php
│       └── StoreSubjectRequest.php
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Subject.php
│   ├── CodingLog.php
│   └── AiFeedbackLog.php
└── Services/
    └── AiFeedbackService.php                 ← Claude API integration

database/
├── migrations/                               ← 6 migration files
└── seeders/                                  ← roles, users, subjects, logs

resources/views/
├── layouts/
│   ├── app.blade.php                         ← main sidebar layout
│   └── guest.blade.php                       ← auth layout
├── auth/                                     ← login, register, forgot-password
├── student/
│   ├── dashboard.blade.php
│   └── logs/                                 ← index, create, edit, show
├── instructor/
│   ├── dashboard.blade.php
│   └── students/                             ← index, show
├── subjects/                                 ← index, create, edit, show
└── errors/
    └── 403.blade.php
```

---

## 🚀 Setup Instructions

### 1. Create a new Laravel 11 project

```bash
laravel new codetrack-ai
cd codetrack-ai
```

### 2. Install Laravel Breeze

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### 3. Copy this project's files over the Laravel skeleton

Place all files from this package into their matching paths in your Laravel project.

### 4. Configure your `.env`

```env
APP_NAME="CodeTrack AI"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=codetrack_ai
DB_USERNAME=root
DB_PASSWORD=

# Optional: Anthropic API for AI code feedback
ANTHROPIC_API_KEY=sk-ant-your-key-here
```

### 5. Run migrations and seed

```bash
php artisan migrate:fresh --seed
```

### 6. Register the role middleware in `bootstrap/app.php`

This is already included in the provided `bootstrap/app.php`. The alias `'role'` maps to `RoleMiddleware::class`.

### 7. Start the server

```bash
php artisan serve
```

Visit `http://localhost:8000`

---

## 🔑 Demo Accounts

| Role       | Email                       | Password   |
|------------|-----------------------------|------------|
| Instructor | instructor@codetrack.dev    | password   |
| Student    | juan@student.dev            | password   |
| Student    | ana@student.dev             | password   |
| Student    | carlos@student.dev          | password   |

---

## 🤖 AI Integration (Optional)

The AI feedback uses the **Anthropic Claude API**.

1. Get an API key at [console.anthropic.com](https://console.anthropic.com)
2. Add `ANTHROPIC_API_KEY=sk-ant-...` to your `.env`
3. Students can click **Get AI Feedback** on any log with a code snippet

The `AiFeedbackService` builds a structured prompt including the student's language, subject, difficulty, and code, then returns actionable mentor-style feedback.

---

## 🗄️ Database Schema

```
roles           id, name, display_name
users           id, name, email, password, role_id, student_id
subjects        id, name, code, description, color, created_by
subject_user    id, subject_id, user_id            ← pivot
coding_logs     id, user_id, subject_id, title, description,
                programming_language, hours, minutes,
                log_date, code_snippet, difficulty
ai_feedback_logs id, coding_log_id, user_id, prompt_sent,
                  feedback_received, model_used, tokens_used, status
```

---

## 🛡️ Role-Based Access Control

| Route prefix      | Middleware              | Access        |
|-------------------|-------------------------|---------------|
| `/student/*`      | `auth, role:student`    | Students only |
| `/instructor/*`   | `auth, role:instructor` | Instructors only |
| `/subjects/*`     | `auth, role:instructor` | Instructors only |
| `/dashboard`      | `auth`                  | Redirects by role |

---

## 📊 Key Eloquent Relationships

```php
// User
User → belongsTo(Role)
User → belongsToMany(Subject)     // enrolled subjects
User → hasMany(CodingLog)
User → hasMany(AiFeedbackLog)

// Subject
Subject → belongsToMany(User)     // enrolled students
Subject → hasMany(CodingLog)

// CodingLog
CodingLog → belongsTo(User)
CodingLog → belongsTo(Subject)
CodingLog → hasMany(AiFeedbackLog)
```

---

## 🛠️ Tech Stack

- **Laravel 11** — MVC framework
- **MySQL** — Relational database
- **Laravel Breeze** — Starter auth kit
- **Blade Templates** — Server-side rendering
- **Bootstrap 5.3** — UI framework
- **Chart.js 4** — Dashboard charts
- **Bootstrap Icons** — Icon set
- **Anthropic Claude API** — AI code feedback
- **JetBrains Mono** — Code font
