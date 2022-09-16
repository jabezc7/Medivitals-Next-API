<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutomationsController;
use App\Http\Controllers\Api\CommandsController;
use App\Http\Controllers\Api\GroupsController;
use App\Http\Controllers\Api\NotesController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\OrderingController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\SectionsController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TemplatesController;
use App\Http\Controllers\Api\TypesController;
use App\Http\Controllers\Api\UploadsController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\PatientsController;
use App\Http\Controllers\Api\DevicesController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\ExportDataController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\PayloadController;
use Illuminate\Support\Facades\Route;
use Laravel\Vapor\Contracts\SignedStorageUrlController;

Route::middleware('api')->group(function () {
    Route::post('payload', PayloadController::class);
    Route::post('command', CommandsController::class);

    Route::post('authenticate', [AuthController::class, 'authenticate']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('check-token', [AuthController::class, 'checkToken']);

    Route::middleware(['auth:sanctum'])->group(function () {
        // Portal
        Route::prefix('portal')->group(function() {
            Route::get('user-dashboard', [PortalController::class, 'userDashboard'])->name('portal.user-dashboard');
            Route::post('update-frequencies', [PortalController::class, 'updateFrequencies'])->name('portal.update-frequencies');
        });

        // General
        Route::post('upload', [UploadsController::class, 'upload'])->name('upload');

        Route::get('me', [AuthController::class, 'getAuthenticatedUser']);

        // Users
        Route::resource('users', UsersController::class);
        Route::post('/users/get-list',  [UsersController::class, 'get']);

        // Groups
        Route::post('groups/{id}/toggle', [GroupsController::class, 'toggle'])->name('groups.toggle');
        Route::get('groups/form-data', [GroupsController::class, 'formData'])->name('groups.form-data');
        Route::resource('groups', GroupsController::class);

        // Permissions
        Route::get('permissions/form-data', [PermissionsController::class, 'formData'])->name('permissions.form-data');
        Route::post('permissions/{id}/toggle', [PermissionsController::class, 'toggle'])->name('permissions.toggle');
        Route::resource('permissions', PermissionsController::class);

        // Sections
        Route::post('sections/{id}/toggle', [SectionsController::class, 'toggle'])->name('sections.toggle');
        Route::resource('sections', SectionsController::class);

        // Types
        Route::get('types/form-data', [TypesController::class, 'formData'])->name('types.form-data');
        Route::post('types/{id}/toggle', [TypesController::class, 'toggle'])->name('types.toggle');
        Route::post('types/{id}/copy', [TypesController::class, 'copy'])->name('types.copy');
        Route::post('types/groups', [TypesController::class, 'storeGroup'])->name('types.store.group');
        Route::get('types/groups', [TypesController::class, 'groups'])->name('types.groups');
        Route::resource('types', TypesController::class);

        // Vapor Storage
        Route::post('vapor/signed-storage-url', [SignedStorageUrlController::class, 'store'])->name('vapor.signed-storage-url');
        Route::post('vapor/upload', [UploadsController::class, 'vapor'])->name('vapor.upload');

        // Settings
        Route::resource('settings', SettingsController::class);

        // Notes
        Route::resource('notes', NotesController::class);
        Route::get('notes/get-notes-patient-id/{patient_id}', [NotesController::class, 'getNotesByPatientID'])->name('notes.patients');

        // Ordering
        Route::post('ordering', [OrderingController::class, 'update'])->name('ordering.update');

        // Templates
        Route::resource('templates', TemplatesController::class);
        Route::post('templates/sms', [TemplatesController::class, 'getSmsTemplates']);

        // Devices
		Route::resource('devices', DevicesController::class);
		Route::post('devices/update-frequency', [DevicesController::class, 'updateFrequency'])->name('update.frequency');

		// Patients
		Route::post('patients/addDevice', [PatientsController::class, 'addDevice'])->name('patients.add-device');
		Route::post('patients/unlinkDevice', [PatientsController::class, 'unlinkDevice'])->name('patients.unlink-device');
		Route::post('patients/get-vital-stats', [PatientsController::class, 'getVitalStats'])->name('patients.get-vital-stats');
        Route::post('patients/get-list',  [PatientsController::class, 'get']);
        Route::post('patients/{id}/viewed',  [PatientsController::class, 'storeView']);
        Route::resource('patients', PatientsController::class);

        // Data
		Route::resource('data', DataController::class);
        Route::post('data/export', [ExportDataController::class, 'store'])->name('data.export.store');
        Route::get('data/export', [ExportDataController::class, 'index'])->middleware('signed')->name('data.export');

        // Sms
        Route::post('sms/send', [SmsController::class, 'send'])->name('sms.send');
        Route::post('sms/get-sms-by-user', [SmsController::class, 'getUserSMS']);

		// Notifications
		Route::resource('notifications', NotificationsController::class);

		// Automations
		Route::resource('automations', AutomationsController::class);

		/* INSERT ROUTES HERE */
        /* IMPORTANT - Leave the line above - Add New Routes above INSERT ROUTES HERE */
    });
});
