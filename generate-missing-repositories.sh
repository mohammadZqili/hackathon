#!/bin/bash

# Generate missing repositories for SystemAdmin entities

echo "Generating missing repositories..."

# Create Repositories directory if not exists
mkdir -p app/Repositories

# List of entities to generate
entities=(
    "Organization"
    "Speaker"
    "Edition"
    "Setting"
    "Checkin"
    "Report"
    "Dashboard"
)

# Function to create a repository file
create_repository() {
    local entity=$1
    local filename="app/Repositories/${entity}Repository.php"
    
    # Check if file already exists
    if [ -f "$filename" ]; then
        echo "Repository already exists: $filename"
        return
    fi
    
    cat > "$filename" << 'EOF'
<?php

namespace App\Repositories;

use App\Models\ENTITY_NAME;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ENTITY_NAMERepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ENTITY_NAME());
    }

    /**
     * Get paginated ENTITY_LOWER_PLURAL with filters
     */
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        // Add relationships based on entity
        $this->addRelationships($query);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $this->applySearchFilters($q, $search);
            });
        }

        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Find with full details
     */
    public function findWithFullDetails(int $id): ?ENTITY_NAME
    {
        $query = $this->query();
        $this->addRelationships($query);
        return $query->find($id);
    }

    /**
     * Get statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
        ];
    }

    /**
     * Get for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->query();
        $this->addRelationships($query);

        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $this->applySearchFilters($q, $search);
            });
        }

        return $query->get();
    }

    /**
     * Check if entity has dependencies
     */
    public function hasDependencies(int $id): bool
    {
        // Override in specific repository to check for actual dependencies
        return false;
    }

    /**
     * Add relationships to query
     */
    protected function addRelationships($query): void
    {
        // Override in specific repository to add relationships
    }

    /**
     * Apply search filters
     */
    protected function applySearchFilters($query, string $search): void
    {
        // Override in specific repository for entity-specific search
        $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Apply edition filter
     */
    protected function applyEditionFilter($query, $editionId): void
    {
        // Override in specific repository if entity has edition relationship
        if ($this->model->getTable() !== 'hackathon_editions') {
            $query->where('edition_id', $editionId);
        }
    }
}
EOF

    # Replace placeholders
    local entity_lower=$(echo "$entity" | tr '[:upper:]' '[:lower:]')
    local entity_plural=$(echo "${entity}s" | sed 's/ys$/ies/')
    local entity_lower_plural=$(echo "$entity_plural" | tr '[:upper:]' '[:lower:]')
    
    sed -i "s/ENTITY_NAME/$entity/g" "$filename"
    sed -i "s/ENTITY_LOWER/$entity_lower/g" "$filename"
    sed -i "s/ENTITY_PLURAL/$entity_plural/g" "$filename"
    sed -i "s/ENTITY_LOWER_PLURAL/$entity_lower_plural/g" "$filename"
    
    echo "Created repository: $filename"
}

# Generate repositories for each entity
for entity in "${entities[@]}"; do
    create_repository "$entity"
done

# Special handling for specific repositories
echo "Customizing specific repositories..."

# Customize OrganizationRepository
if [ -f "app/Repositories/OrganizationRepository.php" ]; then
    cat > "app/Repositories/OrganizationRepository.php" << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrganizationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Organization());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->withCount('speakers');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('website', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?Organization
    {
        return $this->query()
            ->with(['speakers'])
            ->withCount('speakers')
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        return [
            'total' => $query->count(),
            'companies' => (clone $query)->where('type', 'company')->count(),
            'universities' => (clone $query)->where('type', 'university')->count(),
            'sponsors' => (clone $query)->where('type', 'sponsor')->count(),
        ];
    }

    public function hasDependencies(int $id): bool
    {
        $org = $this->find($id);
        return $org && $org->speakers()->exists();
    }
}
EOF
fi

# Customize SpeakerRepository
if [ -f "app/Repositories/SpeakerRepository.php" ]; then
    cat > "app/Repositories/SpeakerRepository.php" << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Speaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SpeakerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Speaker());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['organization', 'workshops']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('expertise', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        if (!empty($filters['edition_id'])) {
            $query->whereHas('workshops', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?Speaker
    {
        return $this->query()
            ->with(['organization', 'workshops.edition'])
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->whereHas('workshops', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        return [
            'total' => $query->count(),
            'with_workshops' => (clone $query)->has('workshops')->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];
    }

    public function hasDependencies(int $id): bool
    {
        $speaker = $this->find($id);
        return $speaker && $speaker->workshops()->exists();
    }
}
EOF
fi

echo "Repository generation complete!"

chmod +x generate-missing-repositories.sh