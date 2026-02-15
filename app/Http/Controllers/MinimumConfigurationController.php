<?php

namespace App\Http\Controllers;

use App\Models\BillingProfile;
use App\Models\Nas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MinimumConfigurationController extends Controller
{
    public function index()
    {
        if (!$this->checkExamAttendance()) {
            return redirect()->route('exam.index');
        }

        if (!$this->checkBillingProfile()) {
            return redirect()->route('temp_billing_profiles.create');
        }

        if (!$this->checkRouterRegistration()) {
            return redirect()->route('routers.create');
        }

        if (!$this->checkCustomerData()) {
            return redirect()->route('pppoe_customers_import.create');
        }

        if (!$this->checkOperatorBillingProfile()) {
            return redirect()->route('operators.billing_profiles.create');
        }

        if (!$this->checkPackageAssignment()) {
            return redirect()->route('operators.master_packages.create');
        }

        if (!$this->checkPackagePricing()) {
            return redirect()->route('packages.edit');
        }

        if (!$this->checkBackupSettings()) {
            return redirect()->route('backup_settings.create');
        }

        if (!$this->checkProfileCompletion()) {
            return redirect()->route('operators.profile.create');
        }

        return redirect()->route('dashboard');
    }

    private function checkExamAttendance(): bool
    {
        if (config('consumer.exam_attendance')) {
            // Placeholder for actual exam attendance check logic.
            // For now, if exam attendance is enabled, assume it needs to be completed.
            // A more robust solution would check a user_exam_status table or similar.
            return false; // Assuming it needs to be attended/passed if enabled
        }
        return true;
    }

    private function checkBillingProfile(): bool
    {
        return BillingProfile::where('tenant_id', Auth::user()->id)->count() > 0;
    }

    private function checkRouterRegistration(): bool
    {
        return Nas::where('tenant_id', Auth::user()->id)->count() > 0;
    }

    private function checkCustomerData(): bool
    {
        // Logic to check for customer data or import request
        // Assuming App\Models\Customer and App\Models\CustomerImportRequest exist
        return \App\Models\Customer::where('tenant_id', Auth::user()->id)->count() > 0 ||
               \App\Models\CustomerImportRequest::where('tenant_id', Auth::user()->id)->where('status', 'pending')->count() > 0;
    }

    private function checkOperatorBillingProfile(): bool
    {
        // Logic to check if operator has assigned billing profile
        // Assuming a many-to-many relationship between User and BillingProfile
        return Auth::user()->billingProfiles()->count() > 0;
    }

    private function checkPackageAssignment(): bool
    {
        // Logic to check if packages are created from master packages
        // Assuming App\Models\Package exists and has a tenant_id or admin_id
        return \App\Models\Package::where('admin_id', Auth::user()->id)->count() > 0;
    }

    private function checkPackagePricing(): bool
    {
        // Logic to check if all packages have price > 1
        // Assuming App\Models\Package exists and has a tenant_id or admin_id and a 'name' field
        return \App\Models\Package::where('admin_id', Auth::user()->id)
            ->where('name', '!=', 'Trial') // Exclude Trial packages
            ->where('price', '<=', 1)
            ->count() === 0;
    }

    private function checkBackupSettings(): bool
    {
        // Logic to check if backup settings are configured
        // Assuming App\Models\BackupSetting exists and has a tenant_id or admin_id
        return \App\Models\BackupSetting::where('admin_id', Auth::user()->id)->count() > 0;
    }

    private function checkProfileCompletion(): bool
    {
        return !is_null(Auth::user()->company_in_native_lang);
    }
}
