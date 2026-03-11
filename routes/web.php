<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\RegUserController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AgreementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin routes - protected by admin middleware
Route::middleware('admin')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/login-logs', [LoginLogController::class, 'index'])->name('login-logs.index');
        
        // Registered Users
        Route::get('/regusers', [RegUserController::class, 'index'])->name('regusers.index');
        Route::get('/regusers/trash', [RegUserController::class, 'trash'])->name('regusers.trash');
        Route::post('/regusers/preview', [RegUserController::class, 'preview'])->name('regusers.preview');
        Route::get('/regusers/{reguser}/edit', [RegUserController::class, 'edit'])->name('regusers.edit');
        Route::put('/regusers/{reguser}', [RegUserController::class, 'update'])->name('regusers.update');
        Route::delete('/regusers/{reguser}', [RegUserController::class, 'destroy'])->name('regusers.destroy');
        Route::patch('/regusers/{reguser}/restore', [RegUserController::class, 'restore'])->name('regusers.restore');
        Route::delete('/regusers/{reguser}/force-delete', [RegUserController::class, 'forceDelete'])->name('regusers.force-delete');
        
        // Export
        Route::get('/reports', [ExportController::class, 'index'])->name('reports.index');
        Route::post('/reports/preview', [ExportController::class, 'preview'])->name('reports.preview');
        Route::post('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/pdf', [ExportController::class, 'exportPdf'])->name('export.pdf');

        // Facilities
        Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
        Route::get('/facilities/trash', [FacilityController::class, 'trash'])->name('facilities.trash');
        Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
        Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
        Route::patch('/facilities/{facility}/restore', [FacilityController::class, 'restore'])->name('facilities.restore');
        Route::delete('/facilities/{facility}/force-delete', [FacilityController::class, 'forceDelete'])->name('facilities.force-delete');

        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/services/trash', [ServiceController::class, 'trash'])->name('services.trash');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::patch('/services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');
        Route::delete('/services/{service}/force-delete', [ServiceController::class, 'forceDelete'])->name('services.force-delete');

        // Activities
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');

        // Agreements (Privacy Policy & Terms of Service)
        Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
        Route::post('/agreements', [AgreementController::class, 'update'])->name('agreements.update');

        // Super Admin only routes
        Route::middleware('super_admin')->group(function () {
            // Admin Management
            Route::resource('admins', AdminManagementController::class);
            Route::post('admins/{admin}/toggle-status', [AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');
            Route::get('admins-trash', [AdminManagementController::class, 'trash'])->name('admins.trash');
            Route::patch('admins/{admin}/restore', [AdminManagementController::class, 'restore'])->name('admins.restore');
            Route::delete('admins/{admin}/force-delete', [AdminManagementController::class, 'forceDelete'])->name('admins.force-delete');
            
            // Role Management
            Route::resource('roles', RoleManagementController::class);
            Route::get('roles-trash', [RoleManagementController::class, 'trash'])->name('roles.trash');
            Route::patch('roles/{role}/restore', [RoleManagementController::class, 'restore'])->name('roles.restore');
            Route::delete('roles/{role}/force-delete', [RoleManagementController::class, 'forceDelete'])->name('roles.force-delete');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
