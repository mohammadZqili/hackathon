# Controller Updates for Edition-Based Access Control

## Controllers that need updating:

### 1. **IdeaController.php**
```php
// Add at the top
use App\Traits\HasEditionAccess;

class IdeaController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $query = Idea::with(['team', 'track', 'reviewer']);
        
        // Apply edition filter
        $query = $this->applyEditionFilter($query);

        // ... rest of the filtering logic ...
        
        // Update statistics to only count from accessible editions
        $editionIds = $this->getAccessibleEditionIds();
        $statistics = [
            'total' => Idea::whereIn('edition_id', $editionIds)->count(),
            'draft' => Idea::whereIn('edition_id', $editionIds)->where('status', 'draft')->count(),
            // ... etc for all statistics
        ];

        // Pass editions for filter dropdown
        $editions = $this->getEditionsForFilter();
        
        return Inertia::render('HackathonAdmin/Ideas/Index', [
            'ideas' => $ideas,
            'statistics' => $statistics,
            'editions' => $editions,
            'currentEditionId' => $this->getCurrentEditionId(),
            // ...
        ]);
    }

    public function show(Idea $idea)
    {
        // Check edition access
        $this->authorizeEditionAccess($idea);
        
        // ... rest of the method
    }

    public function processReview(Request $request, Idea $idea)
    {
        // Check edition access
        $this->authorizeEditionAccess($idea);
        
        // ... rest of the method
    }

    // Apply similar changes to all other methods
}
```

### 2. **TeamController.php**
```php
use App\Traits\HasEditionAccess;

class TeamController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $query = Team::with(['leader', 'members', 'idea', 'edition']);
        
        // Apply edition filter
        $query = $this->applyEditionFilter($query);

        // ... rest of the logic ...
    }

    public function show(Team $team)
    {
        $this->authorizeEditionAccess($team);
        // ... rest
    }

    public function update(Request $request, Team $team)
    {
        $this->authorizeEditionAccess($team);
        // ... rest
    }

    public function destroy(Team $team)
    {
        $this->authorizeEditionAccess($team);
        // ... rest
    }
}
```

### 3. **TrackController.php**
```php
use App\Traits\HasEditionAccess;

class TrackController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $query = Track::with(['teams', 'ideas', 'edition', 'hackathon'])
            ->withCount(['teams', 'ideas']);

        // Apply edition filter
        $query = $this->applyEditionFilter($query);

        // ... rest of filtering ...

        // Update statistics
        $editionIds = $this->getAccessibleEditionIds();
        $statistics = [
            'total' => Track::whereIn('edition_id', $editionIds)->count(),
            'active' => Track::whereIn('edition_id', $editionIds)->where('is_active', true)->count(),
            // ... etc
        ];

        $editions = $this->getEditionsForFilter();

        return Inertia::render('HackathonAdmin/Tracks/Index', [
            'tracks' => $tracks,
            'editions' => $editions,
            'statistics' => $statistics,
            'currentEditionId' => $this->getCurrentEditionId(),
            // ...
        ]);
    }

    // Apply similar pattern to all methods
}
```

### 4. **WorkshopController.php**
```php
use App\Traits\HasEditionAccess;

class WorkshopController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $query = Workshop::with(['edition', 'speakers', 'organizations', 'registrations']);
        
        // Apply edition filter (workshops use hackathon_edition_id)
        $query = $this->applyEditionFilter($query, 'hackathon_edition_id');

        // ... rest of the logic ...
    }

    // Apply to all methods
}
```

### 5. **NewsController.php**
```php
use App\Traits\HasEditionAccess;

class NewsController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $query = News::with(['edition']);
        
        // Apply edition filter (news might use hackathon_edition_id)
        $query = $this->applyEditionFilter($query, 'hackathon_edition_id');

        // ... rest
    }

    // Apply to all methods
}
```

### 6. **DashboardController.php**
```php
use App\Traits\HasEditionAccess;

class DashboardController extends Controller
{
    use HasEditionAccess;

    public function index()
    {
        $editionIds = $this->getAccessibleEditionIds();
        $currentEditionId = $this->getCurrentEditionId();

        // Filter all statistics by edition
        $statistics = [
            'teams' => Team::whereIn('edition_id', $editionIds)->count(),
            'ideas' => Idea::whereIn('edition_id', $editionIds)->count(),
            'workshops' => Workshop::whereIn('hackathon_edition_id', $editionIds)->count(),
            'participants' => Team::whereIn('edition_id', $editionIds)
                ->withCount('members')
                ->get()
                ->sum('members_count'),
            // ... etc
        ];

        // Get recent activities filtered by edition
        $recentIdeas = Idea::whereIn('edition_id', $editionIds)
            ->with(['team', 'track'])
            ->latest()
            ->take(5)
            ->get();

        $editions = $this->getEditionsForFilter();

        return Inertia::render('HackathonAdmin/Dashboard', [
            'statistics' => $statistics,
            'recentIdeas' => $recentIdeas,
            'editions' => $editions,
            'currentEditionId' => $currentEditionId,
            // ...
        ]);
    }
}
```

### 7. **ReportController.php**
```php
use App\Traits\HasEditionAccess;

class ReportController extends Controller
{
    use HasEditionAccess;

    public function index()
    {
        $editionIds = $this->getAccessibleEditionIds();
        
        // All reports should be filtered by accessible editions
        $teamStats = Team::whereIn('edition_id', $editionIds)
            ->selectRaw('edition_id, count(*) as count')
            ->groupBy('edition_id')
            ->get();

        $ideaStats = Idea::whereIn('edition_id', $editionIds)
            ->selectRaw('edition_id, status, count(*) as count')
            ->groupBy('edition_id', 'status')
            ->get();

        // ... etc for all reports
    }

    public function export($type)
    {
        $editionIds = $this->getAccessibleEditionIds();
        
        // Filter export data by editions
        switch($type) {
            case 'teams':
                $data = Team::whereIn('edition_id', $editionIds)->get();
                break;
            case 'ideas':
                $data = Idea::whereIn('edition_id', $editionIds)->get();
                break;
            // ... etc
        }
    }
}
```

### 8. **UserController.php**
```php
use App\Traits\HasEditionAccess;

class UserController extends Controller
{
    use HasEditionAccess;

    public function index(Request $request)
    {
        $editionIds = $this->getAccessibleEditionIds();
        
        // Show only users related to accessible editions
        $query = User::whereHas('teams', function($q) use ($editionIds) {
            $q->whereIn('edition_id', $editionIds);
        })->orWhereHas('reviewedIdeas', function($q) use ($editionIds) {
            $q->whereIn('edition_id', $editionIds);
        });

        // ... rest of logic
    }
}
```

### 9. **CheckinController.php**
```php
use App\Traits\HasEditionAccess;

class CheckinController extends Controller
{
    use HasEditionAccess;

    public function index()
    {
        $editionIds = $this->getAccessibleEditionIds();
        
        // Filter checkins by workshop edition
        $checkins = WorkshopRegistration::whereHas('workshop', function($q) use ($editionIds) {
            $q->whereIn('hackathon_edition_id', $editionIds);
        })->with(['workshop', 'user'])->get();

        // ... rest
    }
}
```

### 10. **EditionController.php**
```php
use App\Traits\HasEditionAccess;

class EditionController extends Controller
{
    use HasEditionAccess;

    public function index()
    {
        // Show only accessible editions
        $editions = $this->getEditionsForFilter();
        
        return Inertia::render('HackathonAdmin/Editions/Index', [
            'editions' => $editions,
            'currentEditionId' => $this->getCurrentEditionId(),
        ]);
    }

    public function show(Edition $edition)
    {
        // Check if user can access this edition
        if (!auth()->user()->canAccessEdition($edition->id)) {
            abort(403, 'You do not have access to this edition.');
        }

        // ... rest
    }

    public function update(Request $request, Edition $edition)
    {
        // Check access
        if (!auth()->user()->canAccessEdition($edition->id)) {
            abort(403, 'You do not have access to this edition.');
        }

        // ... rest
    }
}
```
