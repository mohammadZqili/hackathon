# Type Conversion Fixes for Ideas Management

## Issue Summary
The `assignSupervisor` method was failing with a type error because HTTP request data always comes as strings, but the service method expected integer and float types.

## Root Cause
HTTP request data in Laravel is always received as strings, even for numeric values. However, our service methods had strict type hints expecting `int` and `float` parameters.

## Fixed Methods

### 1. Controller Level Fixes
**File**: `app/Http/Controllers/SystemAdmin/IdeaController.php`

#### assignSupervisor Method
```php
// Before
$this->ideaService->assignSupervisor($idea, $request->supervisor_id);

// After  
$this->ideaService->assignSupervisor($idea, (int) $request->supervisor_id);
```

#### updateScore Method
```php
// Before
$this->ideaService->updateScore($idea, $request->score);

// After
$this->ideaService->updateScore($idea, (float) $request->score);
```

### 2. Service Level Fixes
**File**: `app/Services/IdeaService.php`

#### acceptIdea Method
```php
// Before
'score' => $data['score'] ?? null,

// After
'score' => isset($data['score']) ? (float) $data['score'] : null,
```

#### rejectIdea Method
```php
// Before
'score' => $data['score'] ?? null,

// After
'score' => isset($data['score']) ? (float) $data['score'] : null,
```

#### markForRevision Method
```php
// Before
'score' => $data['score'] ?? null,

// After
'score' => isset($data['score']) ? (float) $data['score'] : null,
```

#### reviewIdea Method
```php
// Before
'review_feedback' => $reviewData['feedback'] ?? null,
'review_score' => $reviewData['score'] ?? null,

// After
'feedback' => $reviewData['feedback'] ?? null,
'score' => isset($reviewData['score']) ? (float) $reviewData['score'] : null,
```

## Additional Fix: Field Name Correction
The `reviewIdea` method was using incorrect field names (`review_feedback` and `review_score`) that don't exist in the Idea model. Fixed to use the correct field names (`feedback` and `score`).

## Model Casts Verification
The Idea model has proper casting for the score field:
```php
protected $casts = [
    'score' => 'decimal:2',
    // ... other casts
];
```

This ensures that values are properly converted to decimal type when saved to the database.

## Why These Fixes Are Important

1. **Type Safety**: Ensures service methods receive the expected data types
2. **Consistency**: All score handling now uses proper float conversion
3. **Database Integrity**: Combined with model casts, ensures proper data storage
4. **Error Prevention**: Prevents TypeError exceptions during runtime

## Testing the Fixes

1. **Test supervisor assignment**: Should now work without type errors
2. **Test score updates**: Both direct updates and review scores should work
3. **Test idea reviews**: All review actions (accept/reject/revision) should handle scores correctly

The fixes maintain backward compatibility while ensuring type safety throughout the application.
