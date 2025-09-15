<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class WorkshopTimeValidation implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     */
    protected array $data = [];

    /**
     * Minimum duration in minutes
     */
    protected int $minDuration;

    /**
     * Maximum duration in hours
     */
    protected int $maxDurationHours;

    public function __construct(int $minDuration = 30, int $maxDurationHours = 8)
    {
        $this->minDuration = $minDuration;
        $this->maxDurationHours = $maxDurationHours;
    }

    /**
     * Set the data under validation.
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!isset($this->data['start_time']) || !$value) {
            return;
        }

        try {
            $startTime = Carbon::parse($this->data['start_time']);
            $endTime = Carbon::parse($value);

            // Check if end time is after start time
            if ($endTime->lessThanOrEqualTo($startTime)) {
                $fail('validation.workshop.end_time_after_start')
                    ->translate([
                        'attribute' => $attribute,
                        'start' => $startTime->format('H:i')
                    ]);
                return;
            }

            // Check minimum duration
            $durationMinutes = $startTime->diffInMinutes($endTime);
            if ($durationMinutes < $this->minDuration) {
                $fail('validation.workshop.minimum_duration')
                    ->translate([
                        'attribute' => $attribute,
                        'duration' => $this->minDuration
                    ]);
                return;
            }

            // Check maximum duration
            $durationHours = $startTime->diffInHours($endTime);
            if ($durationHours > $this->maxDurationHours) {
                $fail('validation.workshop.maximum_duration')
                    ->translate([
                        'attribute' => $attribute,
                        'duration' => $this->maxDurationHours
                    ]);
                return;
            }

            // Check if workshop spans multiple days
            if (!$startTime->isSameDay($endTime)) {
                $fail('validation.workshop.same_day')
                    ->translate(['attribute' => $attribute]);
                return;
            }

        } catch (\Exception $e) {
            $fail('validation.workshop.invalid_date_format')
                ->translate(['attribute' => $attribute]);
        }
    }
}