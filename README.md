# 🚀 Skills Pehle — Modern LMS & Affiliate Ecosystem

<p align="center">
  <img src="public/site_images/logo1.png" alt="Skills Pehle Logo" width="220" onerror="this.style.display='none'"/>
</p>

<p align="center">
  <strong>Next-Generation Digital Learning Management System (LMS) & High-Conversion Affiliate Platform</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Razorpay-Integrated-0C2340?style=for-the-badge&logo=razorpay&logoColor=white" alt="Razorpay">
  <img src="https://img.shields.io/badge/License-Proprietary-yellow?style=for-the-badge" alt="License">
</p>

---

## 📖 About The Project

**Skills Pehle** is an enterprise-ready EdTech and Affiliate platform built on **Laravel 12**. It provides learners with digital career courses, bundle-based curriculums, and automated certification, while empowering an active community with a multi-tier affiliate referral system, instantaneous commission tracking, automated invoice generation, and a seamless payout workflow.

---

## 🌟 Key Features

### 🎓 1. Learning Management System (LMS)
- **Course & Bundle Structure:** Modular courses organized by categories and skill tracks, bundled for maximum value.
- **Secure Video Player:** Video streaming with heartbeat progress tracking, resume-where-left-off, and BunnyCDN / FFmpeg streaming support.
- **Resources & Guides:** Downloadable lesson resources, beginner guides, and structured video series for new learners.
- **Automated Certification:** Dynamic PDF certificate generation upon complete course progress.

### 💼 2. Affiliate & Monetization Engine
- **Unique Referral Links:** Custom referral slugs (`/u/{slug}`) with automated visit logging and session attribution.
- **Multi-Tier Commission Rules:** Configurable commission percentages per bundle/course with manual or automated payouts.
- **Real-Time Analytics:** Interactive 6-month revenue & drill-down earnings charts for affiliates and administrators.
- **Digital Wallet & Withdrawals:** In-app wallet ledger, payout requests, bank details verification, and KYC management.
- **Coupons & Packages:** Coupon creation, discount codes, and coupon package transfers between partners.

### 🏢 3. Student Portal
- Personalized learner dashboard with dynamic chart analytics.
- My Courses library & interactive lesson viewer.
- Gamified Rewards & Leaderboard system.
- Invoicing and transaction history with PDF downloads.
- Career Portal to explore curated job openings and internships.
- Support ticketing system for student queries.

### 🛡️ 4. Administration & Control Panel
- **Role-Based Access Control (RBAC):** Granular permissions powered by Spatie Laravel-Permission (Admin, Student, custom roles).
- **User Management:** Detailed profiles, ban/unban toggles, soft deletes, Excel exports, and one-click account impersonation.
- **Verification Desk:** Comprehensive workflows to verify student KYC identity documents and bank account changes.
- **Payment & Tax Configuration:** Razorpay and Cashfree gateway settings, webhook listeners, and GST/Tax rules.
- **Automated System Cleanup:** Database maintenance tools for log pruning and production optimization.
- **Audit Logs:** Full activity tracking via Spatie Activitylog.

---

## 🛠️ Technology Stack

| Component | Technology |
|---|---|
| **Backend Framework** | [Laravel 12](https://laravel.com/) (PHP 8.2+) |
| **Authentication & RBAC** | Laravel Breeze + [Spatie Permission](https://spatie.be/docs/laravel-permission/) |
| **Frontend & Templating** | Blade Templates + Alpine.js + Chart.js |
| **Styling & Assets** | Tailwind CSS + PostCSS + Vite 7 |
| **Payment Gateways** | Razorpay SDK & Cashfree API with Webhooks |
| **PDF Generation** | DomPDF (`barryvdh/laravel-dompdf`) |
| **Excel Imports/Exports** | Maatwebsite Excel (`maatwebsite/excel`) |
| **Media & Video Processing** | PBMedia Laravel-FFmpeg |
| **Database** | MySQL / MariaDB (Compatible with PostgreSQL / SQLite) |

---

## 📋 System Requirements

Ensure your local or production environment meets the following specifications:

- **PHP**: `^8.2` or higher with extensions: `pdo`, `mbstring`, `openssl`, `curl`, `gd`, `fileinfo`, `zip`
- **Composer**: `v2.x`
- **Node.js**: `18.x` or higher (LTS recommended) & **NPM**
- **Database Server**: MySQL `8.0+` or MariaDB `10.5+`
- **FFmpeg** (Optional, required for video transcoding & thumbnail generation)

---

## ⚡ Quick Start & Installation

Follow these steps to set up the project locally:

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/skills-pehle.git
cd skills-pehle
```

### 2. Install PHP & Composer Dependencies
```bash
composer install
```

### 3. Setup Environment File
Copy `.env.example` to `.env` and configure your database and credentials:
```bash
cp .env.example .env
```

Open `.env` and verify database settings:
```env
APP_NAME="Skills Pehle"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=skills_pehle
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Database Migrations & Seeders
```bash
php artisan migrate --seed
```

> **Note:** The seeder populates initial roles, permissions, states, default settings, career data, and default accounts.

### 6. Create Storage Symlink
```bash
php artisan storage:link
```

### 7. Install Frontend Dependencies & Build Assets
```bash
npm install
npm run build
```

---

## 🏃‍♂️ Running the Application

You can launch the full development suite (Artisan server, Vite asset watcher, and Queue worker) with a single command:

```bash
composer run dev
```

Or run the services individually in separate terminals:

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Hot Reload
npm run dev

# Terminal 3: Queue Worker (Processes payments & email jobs)
php artisan queue:listen --tries=1
```

Access the application in your browser at:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Default Test Credentials

Seeders create the following default accounts for testing:

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@admin.com` | `12345678` |
| **Demo Student** | `student@student.com` | `12345678` |

---

## 📁 Key Directory Structure

```text
skills_pehle/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Course, User, Payout, KYC, Setting Controllers
│   │   │   ├── Student/        # Student Dashboard, LMS, Wallet, Career Controllers
│   │   │   ├── Auth/           # Registration, Login, Payment Initiation Controllers
│   │   │   └── Webhook/        # Razorpay & Cashfree Webhook Controllers
│   │   └── Middleware/         # Role checks, purchase guards, onboarding middleware
│   └── Models/                 # Eloquent models (Course, Bundle, User, Payment, etc.)
├── database/
│   ├── migrations/             # Database schema migrations
│   └── seeders/                # Database seeders (Roles, Permissions, Categories, etc.)
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin dashboard & management Blade views
│   │   ├── student/            # Student learning hub, wallet & course views
│   │   ├── web/                # Public pages (Home, About, Refund, Terms, Contact)
│   │   └── layouts/            # Shared layouts (App, Admin, Student)
│   └── css/ & js/              # Tailwind CSS & JavaScript entry files
├── routes/
│   ├── web.php                 # Public and common routes
│   ├── admin.php               # Protected admin control routes
│   ├── student.php             # Protected student LMS & affiliate routes
│   └── auth.php                # Authentication routes
└── config/                     # Application configurations
```

---

## 🧪 Testing & Code Quality

Run tests using Pest / PHPUnit:
```bash
php artisan test
```

Run Laravel Pint to format code according to PSR standards:
```bash
./vendor/bin/pint
```

---

## 🔒 Security & Best Practices

- **CSRF Protection:** Enabled by default on all web POST/PUT/DELETE routes.
- **Webhook Signature Verification:** Payment webhooks verify HMAC signatures before processing transactions.
- **Role & Permission Enforcements:** Strict route middleware (`role:Admin`, `permission:manage-users`, etc.).
- **Impersonation Safety:** Admins can securely impersonate students and easily exit via `/stop-impersonating`.

---

## 📄 License & Ownership

© 2026 **Skills Pehle Ecosystem Private Limited**. All rights reserved.  
Unauthorized copying, modification, distribution, or public display of any part of this software is strictly prohibited without prior written consent.
