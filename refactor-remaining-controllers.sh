#!/bin/bash

echo "Starting batch refactoring of remaining SystemAdmin controllers..."

# List of controllers that need refactoring
controllers=(
    "OrganizationController"
    "SpeakerController"
    "EditionController"
    "CheckinController"
    "SettingsController"
    "DashboardController"
    "ReportController"
)

# Function to refactor a controller
refactor_controller() {
    local controller=$1
    local entity=$(echo "$controller" | sed 's/Controller$//')
    local entity_lower=$(echo "$entity" | tr '[:upper:]' '[:lower:]')
    local entity_plural=$(echo "${entity}s" | sed 's/ys$/ies/' | sed 's/ss$/ses/')
    local entity_lower_plural=$(echo "$entity_plural" | tr '[:upper:]' '[:lower:]')
    
    local filepath="app/Http/Controllers/SystemAdmin/${controller}.php"
    
    echo "Refactoring $controller..."
    
    # Check if controller exists
    if [ ! -f "$filepath" ]; then
        echo "Controller not found: $filepath - Creating new one..."
        
        # Create new controller
        cat > "$filepath" << 'EOF'
<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\ENTITY_NAMEService;
use App\Services\HackathonEditionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ENTITY_NAME;

class ENTITY_NAMEController extends Controller
{
    protected ENTITY_NAMEService $ENTITY_LOWERService;
    protected HackathonEditionService $editionService;
    
    public function __construct(
        ENTITY_NAMEService $ENTITY_LOWERService,
        HackathonEditionService $editionService
    ) {
        $this->ENTITY_LOWERService = $ENTITY_LOWERService;
        $this->editionService = $editionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->ENTITY_LOWERService->getPaginatedENTITY_PLURAL(
            auth()->user(),
            $request->only(['search', 'status', 'edition_id']),
            $request->get('per_page', 15)
        );
        
        return Inertia::render('SystemAdmin/ENTITY_PLURAL/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $editions = $this->editionService->getAllEditions(auth()->user());
        
        return Inertia::render('SystemAdmin/ENTITY_PLURAL/Create', [
            'editions' => $editions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules());
        
        try {
            $result = $this->ENTITY_LOWERService->createENTITY_NAME($validated, auth()->user());
            return redirect()->route('system-admin.ENTITY_LOWER_PLURAL.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ENTITY_NAME $ENTITY_LOWER)
    {
        $data = $this->ENTITY_LOWERService->getENTITY_NAMEDetails($ENTITY_LOWER->id, auth()->user());
        
        if (!$data) {
            abort(404, 'ENTITY_NAME not found or access denied.');
        }
        
        return Inertia::render('SystemAdmin/ENTITY_PLURAL/Show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ENTITY_NAME $ENTITY_LOWER)
    {
        $data = $this->ENTITY_LOWERService->getENTITY_NAMEDetails($ENTITY_LOWER->id, auth()->user());
        
        if (!$data) {
            abort(404, 'ENTITY_NAME not found or access denied.');
        }
        
        $editions = $this->editionService->getAllEditions(auth()->user());
        
        return Inertia::render('SystemAdmin/ENTITY_PLURAL/Edit', array_merge($data, [
            'editions' => $editions
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ENTITY_NAME $ENTITY_LOWER)
    {
        $validated = $request->validate($this->getValidationRules($ENTITY_LOWER->id));
        
        try {
            $result = $this->ENTITY_LOWERService->updateENTITY_NAME($ENTITY_LOWER->id, $validated, auth()->user());
            return redirect()->route('system-admin.ENTITY_LOWER_PLURAL.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ENTITY_NAME $ENTITY_LOWER)
    {
        try {
            $result = $this->ENTITY_LOWERService->deleteENTITY_NAME($ENTITY_LOWER->id, auth()->user());
            return redirect()->route('system-admin.ENTITY_LOWER_PLURAL.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get validation rules for the entity
     */
    protected function getValidationRules($id = null): array
    {
        // Override in specific controller
        return [
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
EOF
        
        # Replace placeholders
        sed -i "s/ENTITY_NAME/$entity/g" "$filepath"
        sed -i "s/ENTITY_LOWER/$entity_lower/g" "$filepath"
        sed -i "s/ENTITY_PLURAL/$entity_plural/g" "$filepath"
        sed -i "s/ENTITY_LOWER_PLURAL/$entity_lower_plural/g" "$filepath"
        
        echo "Created and refactored: $filepath"
    else
        echo "Controller exists: $filepath - Backing up and refactoring..."
        
        # Create backup
        cp "$filepath" "${filepath}.backup"
        
        # For existing controllers, we need to be more careful
        # Check if it's already refactored
        if grep -q "protected.*Service" "$filepath"; then
            echo "Controller already appears to be refactored: $filepath"
        else
            echo "Refactoring existing controller: $filepath"
            # Since each controller is different, we'll need custom handling
            # For now, just log that it needs manual refactoring
            echo "NOTE: $filepath needs manual refactoring"
        fi
    fi
}

# Process each controller
for controller in "${controllers[@]}"; do
    refactor_controller "$controller"
done

# Special handling for specific controllers

# Settings Controller - Custom implementation
echo "Handling SettingsController specifically..."
cat > "app/Http/Controllers/SystemAdmin/SettingsController.php" << 'EOF'
<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    protected SettingService $settingService;
    
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display settings page
     */
    public function index()
    {
        $settings = $this->settingService->getAllSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Display branding settings
     */
    public function branding()
    {
        $settings = $this->settingService->getBrandingSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Branding', [
            'settings' => $settings
        ]);
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|string',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'footer_text' => 'nullable|string',
        ]);
        
        try {
            $result = $this->settingService->updateBrandingSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display SMTP settings
     */
    public function smtp()
    {
        $settings = $this->settingService->getSmtpSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Smtp', [
            'settings' => $settings
        ]);
    }

    /**
     * Update SMTP settings
     */
    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_from_address' => 'required|email',
            'smtp_from_name' => 'required|string',
        ]);
        
        try {
            $result = $this->settingService->updateSmtpSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
EOF

# Dashboard Controller - Custom implementation
echo "Handling DashboardController specifically..."
cat > "app/Http/Controllers/SystemAdmin/DashboardController.php" << 'EOF'
<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display dashboard
     */
    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData(
            auth()->user(),
            $request->only(['edition_id', 'date_range'])
        );
        
        return Inertia::render('SystemAdmin/Dashboard/Index', $data);
    }

    /**
     * Get chart data
     */
    public function chartData(Request $request)
    {
        $data = $this->dashboardService->getChartData(
            auth()->user(),
            $request->get('type', 'registrations'),
            $request->only(['edition_id', 'date_range'])
        );
        
        return response()->json($data);
    }

    /**
     * Get activity feed
     */
    public function activity(Request $request)
    {
        $data = $this->dashboardService->getActivityFeed(
            auth()->user(),
            $request->get('limit', 20)
        );
        
        return response()->json($data);
    }
}
EOF

echo "Refactoring complete!"
echo "Please review the refactored controllers and adjust as needed."
echo "Some controllers may need manual adjustments based on their specific requirements."