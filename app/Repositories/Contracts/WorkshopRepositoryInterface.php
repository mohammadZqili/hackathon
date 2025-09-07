<?php

namespace App\Repositories\Contracts;

interface WorkshopRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get workshops by hackathon ID
     */
    public function getByHackathon(int $hackathonId, array $filters = []);

    /**
     * Get workshop with registrations
     */
    public function getWithRegistrations(int $workshopId);

    /**
     * Get workshop statistics
     */
    public function getStatistics(int $workshopId);

    /**
     * Register user for workshop
     */
    public function registerUser(int $workshopId, int $userId, array $data = []);

    /**
     * Unregister user from workshop
     */
    public function unregisterUser(int $workshopId, int $userId);

    /**
     * Check user registration status
     */
    public function isUserRegistered(int $workshopId, int $userId): bool;

    /**
     * Mark user as attended
     */
    public function markAttendance(int $workshopId, int $userId, string $barcode = null);

    /**
     * Get user registrations
     */
    public function getUserRegistrations(int $userId, array $filters = []);

    /**
     * Get workshops by speaker
     */
    public function getBySpeaker(int $speakerId);

    /**
     * Get workshops by organization
     */
    public function getByOrganization(int $organizationId);

    /**
     * Get available workshops (not full)
     */
    public function getAvailableWorkshops(array $filters = []);

    /**
     * Get workshop attendance report
     */
    public function getAttendanceReport(int $workshopId);

    /**
     * Bulk register users for workshop
     */
    public function bulkRegisterUsers(int $workshopId, array $userIds);
}
