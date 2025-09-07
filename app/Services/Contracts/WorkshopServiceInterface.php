<?php

namespace App\Services\Contracts;

use App\Models\Workshop;
use App\Models\User;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Collection;

interface WorkshopServiceInterface
{
    public function registerForWorkshop(Workshop $workshop, User $user): ?WorkshopRegistration;
    public function cancelRegistration(int $registrationId, User $user): bool;
    public function generateQRCode(WorkshopRegistration $registration): string;
    public function scanAttendance(string $barcodeData, User $supervisor): array;
    public function getWorkshopAttendanceReport(int $workshopId): array;
    public function getUserRegistrations(User $user): Collection;
}
