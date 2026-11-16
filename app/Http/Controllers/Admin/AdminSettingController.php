<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function index(Request $request): View
    {
        $settings = SystemSetting::all();
        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        return view('admin.settings.index', compact('settings', 'recentLogs'));
    }
}
