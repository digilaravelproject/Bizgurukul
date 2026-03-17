<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\CouponPackageController;
use App\Http\Controllers\Admin\CommissionRuleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ProfileVerificationController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\CommunityController;
use App\Http\Controllers\Admin\CertificateSettingController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\VideoUploadController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard (Accessible to all admins)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Bunny thumbnail proxy — accessible to any authenticated admin (just serves an image)
        Route::get('/courses/lesson/{id}/thumbnail', [CourseController::class, 'lessonThumbnail'])->name('courses.lesson.thumbnail');

        // Settings (managed by manage-settings)
        Route::middleware(['permission:manage-settings'])->group(function () {
            Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('settings.update');

            // Tax Management
            Route::resource('taxes', TaxController::class)->except(['show']);

            // Billing Settings
            Route::get('/settings/billing', [SettingController::class, 'billing'])->name('settings.billing');
            Route::post('/settings/billing', [SettingController::class, 'updateBilling'])->name('settings.billing.update');

            // Email Settings
            Route::get('/settings/email', [SettingController::class, 'emailConfig'])->name('settings.email');
            Route::post('/settings/email', [SettingController::class, 'updateEmailConfig'])->name('settings.email.update');
            Route::post('/settings/email/test', [SettingController::class, 'testEmail'])->name('settings.email.test');

            // Wallet Settings
            Route::get('/settings/wallet', [SettingController::class, 'wallet'])->name('settings.wallet');
            Route::post('/settings/wallet', [SettingController::class, 'updateWallet'])->name('settings.wallet.update');

            // Email Templates
            Route::prefix('email-templates')->name('email-templates.')->group(function () {
                Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
                Route::get('/{key}/edit', [EmailTemplateController::class, 'edit'])->name('edit');
                Route::put('/{key}', [EmailTemplateController::class, 'update'])->name('update');
            });

            Route::resource('achievements', \App\Http\Controllers\Admin\AchievementController::class)->except(['show']);
            Route::post('achievements/{achievement}/toggle-status', [\App\Http\Controllers\Admin\AchievementController::class, 'toggleStatus'])->name('achievements.toggle-status');
        });

        // Transactions (managed by manage-transactions)
        Route::middleware(['permission:manage-transactions'])->group(function () {
            // Order History
            Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
            Route::get('/orders/{id}/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('orders.invoice');
        });

        // Courses & Related (managed by manage-courses)
        Route::middleware(['permission:manage-courses'])->group(function () {
            Route::resource('coupon-packages', CouponPackageController::class);

        // Bundles (managed by manage-bundles)
        Route::middleware(['permission:manage-bundles'])->group(function () {
            Route::resource('bundles', BundleController::class);
        });

        // Certificates (managed by manage-certificates)
        Route::middleware(['permission:manage-certificates'])->group(function () {
            Route::get('certificate-settings', [CertificateSettingController::class, 'index'])->name('certificate.settings');
            Route::post('certificate-settings', [CertificateSettingController::class, 'store'])->name('certificate.settings.store');
        });

            // Categories
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::post('/store', [CategoryController::class, 'store'])->name('store');
                Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
            });

            // Courses
            Route::prefix('courses')->name('courses.')->group(function () {
                Route::get('/', [CourseController::class, 'index'])->name('index');
                Route::get('/create', [CourseController::class, 'create'])->name('create');
                Route::post('/store', [CourseController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [CourseController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [CourseController::class, 'destroy'])->name('delete');
                Route::get('/sub-categories/{id}', [CourseController::class, 'getSubCategories'])->name('subcategories');
                Route::delete('/lesson/{id}/delete', [CourseController::class, 'destroyLesson'])->name('lesson.delete');
                Route::delete('/resource/{id}/delete', [CourseController::class, 'destroyResource'])->name('resource.delete');
                Route::post('/{id}/lesson/store', [CourseController::class, 'storeLesson'])->name('lesson.store');
                Route::post('/lesson/{id}/update', [CourseController::class, 'updateLesson'])->name('lesson.update');
                Route::post('/{id}/resource/store', [CourseController::class, 'storeResource'])->name('resource.store');
                Route::post('/lessons/upload-chunk', [VideoUploadController::class, 'uploadChunk'])->name('lesson.upload-chunk');
            });

            // Lessons (Direct Access)
            Route::prefix('lessons')->name('lessons.')->group(function () {
                Route::get('/all-lessons', [LessonController::class, 'allLessons'])->name('all');
                Route::get('/create/{course_id}', [LessonController::class, 'create'])->name('create');
                Route::post('/store', [LessonController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [LessonController::class, 'edit'])->name('edit');
                Route::delete('/delete/{id}', [LessonController::class, 'destroy'])->name('delete');
            });

            // Video
            Route::post('/lms/upload', [VideoController::class, 'uploadVideo'])->name('video.upload');
            Route::post('/api/video-progress', [VideoController::class, 'updateHeartbeat'])->name('video.progress');

            // Beginner guide management
            Route::get('/beginner-guide', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'index'])
                ->name('beginner-guide');
            Route::get('/resources', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'resources'])
                ->name('resources');
            Route::post('/beginner-guide/store', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'store'])
                ->name('beginner-guide.store');
            // legacy upload route kept for compatibility
            Route::post('/beginner-guide/upload', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'store'])
                ->name('beginner-guide.upload');
            Route::delete('/beginner-guide/{id}', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'destroy'])
                ->name('beginner-guide.destroy');
            Route::post('/beginner-guide/progress', [\App\Http\Controllers\Admin\BeginnerGuideController::class, 'updateProgress'])
                ->name('beginner-guide.progress');

            // Coupons
            Route::controller(CouponController::class)->prefix('coupons')->name('coupons.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });
        });

        // Users Management (managed by manage-users)
        Route::middleware(['permission:manage-users'])->group(function () {
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::post('/store', [UserController::class, 'store'])->name('store');
                Route::get('/{id}/details', [UserController::class, 'show'])->name('show');
                Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
                Route::post('/ban/{id}', [UserController::class, 'toggleBan'])->name('ban');
                Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
                Route::post('/restore/{id}', [UserController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [UserController::class, 'forceDelete'])->name('force.delete');
            });

            // Communities
            Route::prefix('communities')->name('communities.')->group(function () {
                Route::get('/', [CommunityController::class, 'index'])->name('index');
                Route::post('/', [CommunityController::class, 'store'])->name('store');
                Route::put('/{community}', [CommunityController::class, 'update'])->name('update');
                Route::post('/{community}/toggle', [CommunityController::class, 'toggleStatus'])->name('toggle');
                Route::delete('/{community}', [CommunityController::class, 'destroy'])->name('destroy');
            });
        });

        // KYC Management (managed by manage-kyc)
        Route::middleware(['permission:manage-kyc'])->group(function () {
            Route::prefix('kyc-requests')->name('kyc.')->group(function () {
                Route::get('/', [KycController::class, 'index'])->name('index');
                Route::get('/{id}', [KycController::class, 'show'])->name('show');
                Route::post('/{id}/status', [KycController::class, 'updateStatus'])->name('status');
            });
        });

        Route::middleware(['permission:manage-settings'])->group(function () {
            Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        // Roles & Permissions (managed by manage-roles)
        // Also protected by controller middleware, but grouping anyway
        Route::resource('roles', RoleController::class);

        // Transactions, Affiliate, Payouts & verifications (managed by manage-transactions)
        Route::middleware(['permission:manage-transactions'])->group(function () {
            // Affiliate System
            Route::prefix('affiliate')->name('affiliate.')->group(function () {
                // Rules
                Route::get('/rules', [CommissionRuleController::class, 'index'])->name('rules.index');
                Route::post('/rules/store', [CommissionRuleController::class, 'store'])->name('rules.store');
                Route::delete('/rules/delete/{id}', [CommissionRuleController::class, 'destroy'])->name('rules.delete');

                // History & Payments
                Route::get('/history', [AffiliateController::class, 'history'])->name('history');
                Route::post('/commission/{id}/pay', [AffiliateController::class, 'markAsPaid'])->name('commission.pay');

                // Settings
                Route::get('/settings', [AffiliateController::class, 'settings'])->name('settings');
                Route::post('/settings', [AffiliateController::class, 'updateSettings'])->name('settings.update');

                // Affiliate Users Management
                Route::get('/users', [AffiliateController::class, 'index'])->name('users.index');
                Route::get('/users/{id}/edit', [AffiliateController::class, 'edit'])->name('users.edit');
                Route::put('/users/{id}/update', [AffiliateController::class, 'update'])->name('users.update');
                Route::post('/users/{id}/rules', [AffiliateController::class, 'storeRule'])->name('users.rules.store');
                Route::delete('/users/rules/{id}', [AffiliateController::class, 'deleteRule'])->name('users.rules.delete');
            });

            // Payouts & Verifications
            Route::prefix('payouts')->name('payouts.')->group(function () {
                Route::get('/', [PayoutController::class, 'index'])->name('index');
                Route::post('/approve/{id}', [PayoutController::class, 'approve'])->name('approve');
                Route::post('/reject/{id}', [PayoutController::class, 'reject'])->name('reject');
                Route::get('/check-new', [PayoutController::class, 'checkNew'])->name('check_new');
                Route::post('/early-approve/{id}', [PayoutController::class, 'earlyApproveCommission'])->name('commission.early_approve');
            });

            Route::prefix('verifications')->name('verifications.')->group(function () {
                Route::get('/', [ProfileVerificationController::class, 'index'])->name('index');
                Route::get('/check-new', [ProfileVerificationController::class, 'checkNew'])->name('check_new');
                Route::get('/kyc', [ProfileVerificationController::class, 'kycIndex'])->name('kyc.index');
                Route::post('/kyc/{userId}/approve', [ProfileVerificationController::class, 'kycApprove'])->name('kyc.approve');
                Route::post('/kyc/{userId}/reject', [ProfileVerificationController::class, 'kycReject'])->name('kyc.reject');

                Route::get('/bank', [ProfileVerificationController::class, 'bankIndex'])->name('bank.index');
                Route::post('/bank/initial/{bankId}/process', [ProfileVerificationController::class, 'verifyInitialBank'])->name('bank.verify-initial');
                Route::post('/bank/update/{requestId}/process', [ProfileVerificationController::class, 'processBankUpdate'])->name('bank.process-update');
            });
        });
    });
