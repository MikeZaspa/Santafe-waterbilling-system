<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminConsumerController;
use App\Http\Controllers\PlumberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminReadingController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\WaterRateController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\ConsumerAuthController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ConsumerDashboardController;
use App\Http\Controllers\ConsumerBillingController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\OnlinePaymentController;
use App\Http\Controllers\AccountantManageController;
use App\Http\Controllers\DisconnectionController;
use App\Http\Controllers\ConsumerNotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AdminForgotPasswordController;
use App\Http\Controllers\PlumberAuthController;
use App\Http\Controllers\AccountantAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AccountantNotificationController;
use App\Http\Controllers\Admin\BackupController;

// Database Backup Route
Route::get('/admin/backup-database', [BackupController::class, 'backupDatabase'])->name('admin.backup.database');

// Admin Registration Routes
Route::get('/admin-register', [AuthController::class, 'showRegistrationForm'])->name('admin-register');
Route::post('/admin-register', [AuthController::class, 'register']);

// Admin Login Routes
Route::get('/admin-login', [AuthController::class, 'showLoginForm'])->name('admin-login');
Route::post('/admin-login', [AuthController::class,'login']);

// Admin Dashboard Routes
Route::get('/admin-dashboard', [AuthController::class, 'showDashboard'])->middleware('admin.auth')->name('admin.dashboard');
Route::get('/admin-dashboard', [DashboardController::class, 'index']);

// Admin Consumer Routes
Route::get('/admin-consumer', [AdminConsumerController::class, 'index'])->name('admin-consumer');
Route::prefix('admin-consumer')->group(function() {
    Route::post('/', [AdminConsumerController::class, 'store'])->name('admin.consumer.store');
    Route::get('/create', [AdminConsumerController::class, 'create'])->name('admin.consumer.create');
    Route::get('/{adminConsumer}/edit', [AdminConsumerController::class, 'edit'])->name('admin.consumer.edit');
    Route::get('/{adminConsumer}', [AdminConsumerController::class, 'show'])->name('admin.consumer.show');
    Route::put('/{adminConsumer}', [AdminConsumerController::class, 'update'])->name('admin.consumer.update');
    Route::patch('/{adminConsumer}', [AdminConsumerController::class, 'update']);
    Route::delete('/{adminConsumer}', [AdminConsumerController::class, 'destroy'])->name('admin.consumer.destroy');
});

// Admin Session Validation Routes
Route::get('/admin-check-auth', [AuthController::class, 'checkAuthStatus'])->name('admin.check.auth');
Route::post('/admin-refresh-session', [AuthController::class, 'refreshSession'])->name('admin.refresh.session');
Route::get('/admin-check-auth', function () {
    return response()->json([
        'authenticated' => Auth::guard('admin')->check()
    ]);
});

// Admin Plumber Consumer Routes
Route::get('/admin-plumber-consumer', [AuthController::class, 'showPlumberConsumerForm'])->name('admin.plumber-consumer');
Route::post('admin-plumber-consumer', [AuthController::class, 'plumberconsumer']);

// Billing Routes
Route::get('/consumers/{consumer}/last-reading', [BillingController::class, 'getLastReading']);
Route::get('/billing/last-reading/{consumerId}', [BillingController::class, 'getLastReading'])->name('billing.lastReading');
Route::get('/', [BillingController::class, 'index']);
Route::get('/create', [BillingController::class, 'create']);
Route::post('/', [BillingController::class, 'store']);
Route::resource('billings', BillingController::class);
Route::get('/billings', [BillingController::class, 'index'])->name('billings.index');
Route::post('/billings/{billing}/disconnect', [BillingController::class, 'disconnect'])->name('billings.disconnect');

// Water Rates Routes
Route::get('/water-rates', [AuthController::class,'showRatesForm'])->name('water-rates');
Route::post('/water-rates', [AuthController::class,'rates']);
Route::get('/water-rates/all', [WaterRateController::class, 'getAllRates']);
Route::post('/water-rates/calculate', [WaterRateController::class, 'calculateBill']);
Route::get('water-rates', [WaterRateController::class, 'index'])->name('water-rates.index');
Route::post('water-rates', [WaterRateController::class, 'store'])->name('water-rates.store');
Route::get('water-rates/create', [WaterRateController::class, 'create'])->name('water-rates.create');
Route::get('water-rates/{waterRate}/edit', [WaterRateController::class, 'edit'])->name('water-rates.edit');
Route::put('water-rates/{waterRate}', [WaterRateController::class, 'update'])->name('water-rates.update');
Route::delete('water-rates/{waterRate}', [WaterRateController::class, 'destroy'])->name('water-rates.destroy');

// Admin Management Routes
Route::get('/admin-consumer-form', [AuthController::class, 'showManageConsumerForm'])->name('admin-consumer-form');
Route::post('admin-consumer-form', [AuthController::class, 'manageconsumer']);
Route::get('/admin-plumber-dashboard', [AuthController::class,'showPlumberForm'])->name('admin.plumber-dashboard');
Route::post('/admin-plumber-dashboard', [AuthController::class,'plumber']);
Route::get('/admin-accountant-dashboard', [AuthController::class, 'showAccountantForm'])->name('admin.accountant-dashboard');
Route::post('/admin-accountant-dashboard', [AuthController::class,'accountant']);
Route::get('/admin-accountant-consumer', [AuthController::class, 'showAccountantConsumerForm'])->name('admin.accountant-consumer');
Route::post('/admin-accountant-consumer', [AuthController::class,'accountantconsumer']);
Route::get('/admin-accountant-reports', [AuthController::class, 'showAccountantReportsForm'])->name('admin.accountant-reports');
Route::post('/admin-accountant-reports', [AuthController::class,'accountantreports']);
Route::get('/consumer-portal', [AuthController::class, 'showConsumerPortalForm'])->name('consumer-portal');
Route::post('/consumer-portal', [AuthController::class,'consumerportal']);
Route::get('/admin-accountant', [AuthController::class, 'showAdminAccountant'])->name('admin-accountant');
Route::post('/admin-accountant', [AuthController::class,'adminaccountant']);
Route::get('/consumer-history', [AuthController::class, 'showHistoryForm'])->name('consumer-history');
Route::post('/consumer-history', [AuthController::class,'consumerhistory']);
Route::get('/consumer-dashboard', [AuthController::class, 'showPaymentForm'])->name('consumer-dashboard');
Route::post('/consumer-dashboard', [AuthController::class,'consumerpayment']);
Route::get('/consumer-history', [ConsumerController::class, 'history'])->name('consumer.history');
Route::get('/consumer-paid', [AuthController::class, 'showPaidForm'])->name('consumer-paid');
Route::post('/consumer-paid', [AuthController::class,'consumerpaid']);
Route::get('/online-billing', [AuthController::class, 'showOnlineBillingForm'])->name('online-billing');
Route::post('/online-billing', [AuthController::class,'onlinebilling']);
Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('verify');
Route::post('/verify', [AuthController::class,'consumerverify']);

// Verification Routes
Route::get('/verify', [AuthController::class, 'showVerifyForm'])->name('verify');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('verify-code');
Route::post('/resend-code', [AuthController::class, 'resendCode'])->name('resend-code');

// Consumer Auth Routes
Route::prefix('consumer')->group(function () {
    Route::get('/login', [ConsumerAuthController::class, 'showLoginForm'])->name('consumer.login');
    Route::post('/login', [ConsumerAuthController::class, 'login']);
    Route::post('/logout', [ConsumerAuthController::class, 'logout'])->name('consumer.logout');
});

// Consumer Billing Routes
Route::middleware(['auth:consumer'])->group(function () {
    Route::get('/consumer/current-billing/{consumerId}', [ConsumerBillingController::class, 'getCurrentBilling']);
});

// Consumer Auth Routes (Additional)
Route::get('/consumer-login', [ConsumerAuthController::class, 'showLoginForm'])->name('consumer.portal');
Route::post('/consumer-login', [ConsumerAuthController::class, 'login']);
Route::post('/admin-logout', [ConsumerAuthController::class, 'logout'])->name('consumer.logout');

// Consumer Dashboard Routes
Route::middleware(['consumer.auth'])->group(function () {
    Route::get('/consumer/history', [ConsumerDashboardController::class, 'history'])->name('consumer.history');
    Route::get('/consumer/payment', [ConsumerDashboardController::class, 'payment'])->name('consumer.payment');
    Route::post('/consumer/payment', [ConsumerDashboardController::class, 'processPayment']);
});

// Accountant Dashboard Routes
Route::get('/admin-accountant-dashboard', [AccountantDashboardController::class, 'index']);

// Accountant Reports Routes
Route::get('/accountant/reports/data', [ReportController::class, 'data'])->name('accountant.reports.data');
Route::get('/accountant/billings/{billing}/details', [AccountantController::class, 'getBillingDetails'])
    ->name('accountant.billings.details');
Route::get('/accountant/billings/{id}/receipt', [AccountantController::class, 'getReceiptData'])->name('accountant.billings.receipt');

// Accountant Billing Routes
Route::prefix('accountant')->group(function() {
    Route::get('/billings', [AccountantController::class, 'index'])->name('accountant.billings');
    Route::get('/billings/data', [AccountantController::class, 'getBillings'])->name('accountant.billings.data');
    Route::get('/billings/last-reading/{consumerId}', [AccountantController::class, 'getLastReading']);
    Route::post('/billings', [AccountantController::class, 'store'])->name('accountant.billings.store');
    Route::get('/billings/{id}/edit', [AccountantController::class, 'edit'])->name('accountant.billings.edit');
    Route::put('/billings/{id}', [AccountantController::class, 'update'])->name('accountant.billings.update');
    Route::delete('/billings/{id}', [AccountantController::class, 'destroy'])->name('accountant.billings.destroy');
});
Route::get('/accountant/billings/existing', [AccountantController::class, 'getExistingBilling']);

// Consumer Billing and Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/consumer/billings/data', [ConsumerBillingController::class, 'getBillingsData'])->name('consumer.billings.data');
    Route::get('/consumer/billings/{id}/details', [ConsumerBillingController::class, 'getBillingDetails']);
    Route::post('/consumer/payments/process', [PaymentController::class, 'processPayment']);
    Route::get('/consumer/payments/receipt/{id}', [PaymentController::class, 'getReceipt']);
});
Route::post('/payments/process', [PaymentController::class, 'processPayment'])->name('payments.process');
Route::get('/consumer/payment-history', [PaymentHistoryController::class, 'index'])->name('consumer.payment.history');

// Account Management Routes
Route::prefix('account-management')->group(function() {
    Route::get('/', [AccountManagementController::class, 'index'])->name('account-management.index');
    Route::get('/data', [AccountManagementController::class, 'data'])->name('account-management.data');
    Route::post('/', [AccountManagementController::class, 'store'])->name('account-management.store');
    Route::get('/{id}/edit', [AccountManagementController::class, 'edit'])->name('account-management.edit');
    Route::put('/{id}', [AccountManagementController::class, 'update'])->name('account-management.update');
    Route::delete('/{id}', [AccountManagementController::class, 'destroy'])->name('account-management.destroy');
});

// Plumber Dashboard Routes
Route::get('/admin-plumber-dashboard', [ReadingController::class, 'index']);

// Plumber Management Routes
Route::get('/admin-plumber', [PlumberController::class, 'index'])->name('admin-plumber');
Route::prefix('admin-plumber')->group(function() {
    Route::post('/', [PlumberController::class, 'store'])->name('admin.plumber.store');
    Route::get('/create', [PlumberController::class, 'create'])->name('admin.plumber.create');
    Route::get('/{plumber}/edit', [PlumberController::class, 'edit'])->name('admin.plumber.edit');
    Route::get('/{plumber}', [PlumberController::class, 'show'])->name('admin.plumber.show');
    Route::put('/{plumber}', [PlumberController::class, 'update'])->name('admin.plumber.update');
    Route::patch('/{plumber}', [PlumberController::class, 'update']);
    Route::delete('/{plumber}', [PlumberController::class, 'destroy'])->name('admin.plumber.destroy');
});

// Online Billing Routes
Route::prefix('online-billing')->group(function () {
    Route::get('/', [OnlineBillingController::class, 'index'])->name('online-billing.index');
    Route::get('/data', [OnlineBillingController::class, 'index'])->name('online-billing.data');
    Route::get('/consumers', [OnlineBillingController::class, 'getConsumers'])->name('online-billing.consumers');
    Route::post('/', [OnlineBillingController::class, 'store'])->name('online-billing.store');
    Route::get('/{id}', [OnlineBillingController::class, 'show'])->name('online-billing.show');
    Route::put('/{id}', [OnlineBillingController::class, 'update'])->name('online-billing.update');
    Route::delete('/{id}', [OnlineBillingController::class, 'destroy'])->name('online-billing.destroy');
    Route::get('/last-reading/{id}', [OnlineBillingController::class, 'getLastReading'])->name('online-billing.last-reading');
    Route::post('/calculate', [OnlineBillingController::class, 'calculateWaterBill'])->name('online-billing.calculate');
});

// Consumer Authentication Routes
Route::post('/consumer/login', [ConsumerAuthController::class, 'login']);
Route::post('/consumer/logout', [ConsumerAuthController::class, 'logout']);
Route::get('/consumer/login', [ConsumerAuthController::class, 'showLoginForm'])->name('consumer.login');
Route::post('/consumer/login', [ConsumerAuthController::class, 'login']);
Route::post('/consumer/logout', [ConsumerAuthController::class, 'logout'])->name('consumer.logout');
Route::get('/consumer/dashboard', [ConsumerAuthController::class, 'dashboard'])->name('consumer.dashboard');

// Protected Consumer Routes
Route::middleware('auth:consumer')->group(function () {
    Route::get('/consumer/billings', [ConsumerBillingController::class, 'index']);
    Route::post('/consumer/payment/process', [ConsumerPaymentController::class, 'processPayment']);
});

// Consumer Dashboard Route (Protected)
Route::get('/consumer-dashboard', function() {
    if (!Auth::guard('consumer')->check()) {
        return redirect('/consumer/login');
    }

    $account = Auth::guard('consumer')->user();
    $consumer = $account->consumer;
    $bills = Billing::where('consumer_id', $consumer->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
    
    return view('auth.consumer-dashboard', [
        'consumer' => $consumer,
        'bills' => $bills
    ]);
})->name('consumer.dashboard');

// Consumer Payment Routes
Route::prefix('consumer')->group(function () {
    Route::post('/payment/submit', [OnlinePaymentController::class, 'store'])->name('consumer.payment.submit');
});

// Admin Payment Management Routes
Route::get('/admin/payments/datatable', [OnlinePaymentController::class, 'datatable'])
    ->name('admin.payments.datatable');
Route::get('/admin/payments', [OnlinePaymentController::class, 'datatable'])->name('admin.payments.index');
Route::prefix('admin')->group(function () {
    Route::get('/payments', [OnlinePaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{id}', [OnlinePaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/payments/{id}/verify', [OnlinePaymentController::class, 'verify'])->name('admin.payments.verify');
});

// Accountant Management Routes
Route::get('/admin-accountant', [AccountantManageController::class, 'index'])->name('admin.accountant');
Route::post('/admin-accountant', [AccountantManageController::class, 'store']);
Route::get('/admin-accountant/{id}/edit', [AccountantManageController::class, 'edit']);
Route::put('/admin-accountant/{id}', [AccountantManageController::class, 'update']);
Route::delete('/admin-accountant/{id}', [AccountantManageController::class, 'destroy']);

// Consumer Information Routes
Route::get('/consumer-information', [AuthController::class, 'showInformation'])->name('consumer-information');
Route::post('/consumer-information', [AuthController::class,'consumerinformation']);

// Disconnection Routes
Route::get('/admin-plumber-disconnection', [AuthController::class, 'showDisconnectionForm'])->name('admin-plumber-disconnection');
Route::post('/admin-plumber-disconnection', [AuthController::class,'admindisconnection']);
Route::get('/disconnections', [BillingController::class, 'getDisconnectedConsumers']);
Route::post('/disconnections', [BillingController::class, 'disconnect']);
Route::post('/disconnections/{id}/restore', [BillingController::class, 'restoreDisconnectedConsumer']);
Route::post('/admin-plumber-disconnection/{disconnection}/reconnect', [DisconnectionController::class, 'reconnect'])->name('admin.plumber.disconnection.reconnect');
Route::get('/reconnected-consumers', [ReadingController::class, 'getReconnectedConsumers']);
Route::get('/all-reconnected-consumers', [ReadingController::class, 'getAllReconnectedConsumers']);
Route::post('/disconnections/{id}/restore', [ReadingController::class, 'reconnect']);
Route::get('/dashboard-data', [ReadingController::class, 'getDashboardData'])->name('dashboard.data');
Route::get('/reconnected-consumers', [ReadingController::class, 'getReconnectedConsumers'])->name('reconnected.consumers');

// Main Form Routes
Route::get('/main-form', [AuthController::class, 'showMainForm'])->name('main-form');
Route::post('/main-form', [AuthController::class, 'main']);

// Plumber Login Routes
Route::get('/plumber/login', [PlumberAuthController::class, 'showLoginForm'])->name('plumber.login');
Route::post('/plumber/login', [PlumberAuthController::class, 'login'])->name('plumber.login.submit');

// Accountant Login Routes  
Route::get('/accountant/login', [AccountantAuthController::class, 'showLoginForm'])->name('accountant.login');
Route::post('/accountant/login', [AccountantAuthController::class, 'login'])->name('accountant.login.submit');

// Password Reset Routes
Route::prefix('admin')->group(function () {
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'reset'])->name('password.update');
    Route::post('/verify-reset-code', [AdminAuthController::class, 'verifyResetCode'])->name('password.verify');
});

// Two-factor Authentication Routes
Route::post('/admin-check-credentials', [AuthController::class, 'checkCredentials'])->name('admin-check-credentials');
Route::post('/admin-verify-2fa', [AuthController::class, 'verifyTwoFactor'])->name('admin-verify-2fa');
Route::post('/admin-resend-2fa', [AuthController::class, 'resendTwoFactor'])->name('admin-resend-2fa');

// Password Reset Routes (Additional)
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Consumer Notification Routes
Route::get('/consumer/notifications', [NotificationController::class, 'index']);
Route::get('/consumer/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
Route::post('/consumer/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
Route::post('/consumer/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

// Reconnection Routes
Route::post('/admin-plumber-disconnection/{id}/reconnect', [ReadingController::class, 'reconnectConsumer'])->name('admin.reconnect');

// Notice Routes
Route::get('/admin-accountant-notice', [AuthController::class, 'showNotice'])->name('admin-accountant-notice');
Route::post('admin-accountant-notice', [AuthController::class, 'notice']);
Route::get('/accountant-archieve', [AuthController::class, 'showArchieve'])->name('accountant-archieve');
Route::post('accountant-archieve', [AuthController::class, 'archieve']);
Route::get('/notices/consumers', [NoticeController::class, 'getConsumers'])->name('notices.consumers');

// Notices Routes (Additional)
Route::prefix('notices')->group(function () {
    Route::get('/', [NoticeController::class, 'index'])->name('notices.index');
    Route::get('/consumers', [NoticeController::class, 'getConsumers'])->name('notices.consumers');
    Route::post('/', [NoticeController::class, 'store'])->name('notices.store');
    Route::get('/{notice}', [NoticeController::class, 'show'])->name('notices.show');
    Route::get('/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
    Route::put('/{notice}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    Route::patch('/{notice}/toggle-status', [NoticeController::class, 'toggleStatus'])->name('notices.toggle-status');
});
Route::get('/consumer/notices', [ConsumerAuthController::class, 'getNotices'])->name('consumer.notices');

// Billing Data Routes
Route::get('/accountant/billings/data', [BillingController::class, 'getBillingsData'])->name('accountant.billings.data');
Route::get('/accountant/billings/archived/data', [BillingController::class, 'getArchivedBillingsData'])->name('accountant.billings.archived.data');

// Archive Routes
Route::post('/accountant/billings/{id}/archive', [BillingController::class, 'archive'])->name('accountant.billings.archive');
Route::post('/accountant/billings/{id}/restore', [BillingController::class, 'restore'])->name('accountant.billings.restore');
Route::delete('/accountant/billings/{id}/force-delete', [BillingController::class, 'forceDelete'])->name('accountant.billings.force-delete');
Route::post('/accountant/billings/empty-archive', [BillingController::class, 'emptyArchive'])->name('accountant.billings.empty-archive');
Route::get('/accountant/billings/{id}/archive-details', [BillingController::class, 'getArchiveDetails'])->name('accountant.billings.archive-details');

// Other Billing Routes
Route::get('/billing/last-reading/{consumerId}', [BillingController::class, 'getLastReading']);
Route::get('/accountant/billings/{id}/details', [BillingController::class, 'getBillingDetails']);
Route::get('/accountant/billings/{id}/receipt', [BillingController::class, 'getReceipt']);

// Cut Consumer Routes
Route::post('/cut-consumers', [BillingController::class, 'cutConsumer'])->name('cut-consumers.store');
Route::get('/cut-consumers', [BillingController::class, 'getCutConsumers'])->name('cut-consumers.index');
Route::post('/cut-consumers/{id}/restore', [BillingController::class, 'restoreConsumer'])->name('cut-consumers.restore');

// Admin Logs API Endpoints
Route::get('/admin/logs/api', [DashboardController::class, 'getAdminLogs'])->name('admin.logs.api');
Route::get('/admin/admins/api', [DashboardController::class, 'getAdmins'])->name('admin.admins.api');

// Consumer Dashboard and Profile Routes
Route::get('/consumer-dashboard', [AuthController::class, 'consumerDashboard']);
Route::get('/consumer-profile', [AuthController::class, 'consumerprofile']);
Route::get('/consumer-profile', [ConsumerController::class, 'profile'])->name('consumer.profile');
Route::get('/dashboard-consumer', [ConsumerDashboardController::class, 'index'])->name('consumer.dashboard');

// Two-factor Authentication Routes (Additional)
Route::post('/admin-check-credentials', [AuthController::class, 'checkCredentials'])->name('admin.check-credentials');
Route::post('/plumber/verify-2fa', [PlumberController::class, 'verify2FA'])->name('plumber.verify.2fa');
Route::post('/plumber/resend-2fa', [PlumberController::class, 'resend2FA'])->name('plumber.resend.2fa');

// Consumer Notification Routes (Additional)
Route::post('/consumer/notifications/{id}/read', [ConsumerAuthController::class, 'markNotificationAsRead']);
Route::post('/consumer/notifications/read-all', [ConsumerAuthController::class, 'markAllNotificationsAsRead']);
Route::post('/consumer/notifications/create', [ConsumerAuthController::class, 'createNotification']);

// Accountant Notification Routes
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function() {
    Route::get('/notifications', [AccountantNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [AccountantNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AccountantNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notifications/unread-count', [AccountantNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/create', [AccountantNotificationController::class, 'create'])->name('notifications.create');
});

// Plumber Login Routes (Additional)
Route::get('/plumber-login', [PlumberController::class, 'showLoginForm'])->name('plumber.login');
Route::post('/plumber-login', [PlumberController::class, 'login'])->name('plumber.login.submit');
Route::get('/plumber/dashboard', [PlumberController::class, 'dashboard'])->name('plumber.dashboard');
Route::post('/plumber/logout', [PlumberController::class, 'logout'])->name('plumber.logout');
Route::post('/plumber/verify-2fa', [PlumberController::class, 'verify2FA'])->name('plumber.verify.2fa');
Route::post('/plumber/resend-2fa', [PlumberController::class, 'resend2FA'])->name('plumber.resend.2fa');

// Admin Plumber Management Routes
Route::get('/admin/plumbers', [PlumberController::class, 'index'])->name('admin.plumbers.index');
Route::post('/admin/plumbers', [PlumberController::class, 'store'])->name('admin.plumbers.store');
Route::get('/admin/plumbers/{id}/edit', [PlumberController::class, 'edit'])->name('admin.plumbers.edit');
Route::put('/admin/plumbers/{id}', [PlumberController::class, 'update'])->name('admin.plumbers.update');
Route::delete('/admin/plumbers/{id}', [PlumberController::class, 'destroy'])->name('admin.plumbers.destroy');
Route::get('/admin-plumber-dashboard', [ReadingController::class, 'index'])->name('admin.plumber.dashboard');

// Accountant Login Routes (Additional)
Route::get('/accountant-login', function() {
    return view('auth.accountant-login');
})->name('accountant.login');
Route::post('/accountant-login/submit', [AccountantManageController::class, 'send2FACode'])->name('accountant.login.submit');

// 2FA Routes
Route::get('/accountant-2fa', [AccountantManageController::class, 'show2FAModal'])->name('accountant.2fa.show');
Route::post('/accountant-2fa/verify', [AccountantManageController::class, 'verify2FACode'])->name('accountant.2fa.verify');
Route::post('/accountant-2fa/resend', [AccountantManageController::class, 'resend2FACode'])->name('accountant.2fa.resend');

// Payment Proof Image Route
Route::get('/payment-proof/{id}', [OnlinePaymentController::class, 'getProofImage'])->name('payment.proof.image');

// Consumer Portal Routes (Additional)
Route::get('/consumer-portal', [ConsumerAuthController::class, 'showLoginForm'])->name('consumer.login.form');
Route::post('/consumer/login', [ConsumerAuthController::class, 'login'])->name('consumer.login');
Route::post('/consumer/verify-2fa', [ConsumerAuthController::class, 'verify2FA'])->name('consumer.verify2fa');
Route::post('/consumer/resend-2fa', [ConsumerAuthController::class, 'resend2FA'])->name('consumer.resend2fa');
Route::get('/consumer/dashboard', [ConsumerAuthController::class, 'dashboard'])->name('consumer.dashboard');
Route::post('/consumer/logout', [ConsumerAuthController::class, 'logout'])->name('consumer.logout');

// Session Validation Routes
Route::post('/validate-admin-session', function (Request $request) {
    if (Session::has('admin_id') || Auth::guard('admin')->check()) {
        return response()->json(['valid' => true]);
    } else {
        return response()->json(['valid' => false], 401);
    }
})->name('validate.admin.session');

Route::post('/validate-plumber-session', function (Request $request) {
    if (Session::has('plumber_id') || Auth::guard('plumber')->check()) {
        return response()->json(['valid' => true]);
    } else {
        return response()->json(['valid' => false], 401);
    }
})->name('validate.plumber.session');

Route::post('/validate-accountant-session', function (Request $request) {
    if (Session::has('accountant_id') || Auth::guard('accountant')->check()) {
        return response()->json(['valid' => true]);
    } else {
        return response()->json(['valid' => false], 401);
    }
})->name('validate.accountant.session');

Route::post('/validate-consumer-session', function (Request $request) {
    if (Session::has('consumer_id') || Auth::guard('consumer')->check()) {
        return response()->json(['valid' => true]);
    } else {
        return response()->json(['valid' => false], 401);
    }
})->name('validate.consumer.session');

// Session Refresh Routes
Route::post('/refresh-admin-session', function (Request $request) {
    if (Auth::guard('admin')->check()) {
        // Regenerate session ID to prevent session fixation
        Session::regenerate();
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false], 401);
    }
})->name('refresh.admin.session');

Route::post('/refresh-plumber-session', function (Request $request) {
    if (Auth::guard('plumber')->check()) {
        Session::regenerate();
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false], 401);
    }
})->name('refresh.plumber.session');

Route::post('/refresh-accountant-session', function (Request $request) {
    if (Auth::guard('accountant')->check()) {
        Session::regenerate();
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false], 401);
    }
})->name('refresh.accountant.session');

Route::post('/refresh-consumer-session', function (Request $request) {
    if (Auth::guard('consumer')->check()) {
        Session::regenerate();
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false], 401);
    }
})->name('refresh.consumer.session');

// Logout Routes
Route::post('/accountant/logout', [AccountantManageController::class, 'logout'])->name('accountant.logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Default Route
Route::get('/', function () {
    return view('auth.consumer-portal');
});