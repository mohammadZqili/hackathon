<?php

namespace App\Enums;

enum IdeaStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case NEEDS_REVISION = 'needs_revision';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::NEEDS_REVISION => 'Needs Revision',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::UNDER_REVIEW => 'yellow',
            self::NEEDS_REVISION => 'orange',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
        };
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::NEEDS_REVISION]);
    }

    public function canBeSubmitted(): bool
    {
        return $this === self::DRAFT;
    }

    public function canBeReviewed(): bool
    {
        return in_array($this, [self::SUBMITTED, self::UNDER_REVIEW]);
    }
}
