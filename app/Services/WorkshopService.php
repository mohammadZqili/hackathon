<?php

namespace App\Services;

use App\Repositories\WorkshopRepository;
use Illuminate\Support\Facades\DB;

class WorkshopService extends BaseService
{
    protected $workshopRepo;

    public function __construct(WorkshopRepository $workshopRepo)
    {
        $this->workshopRepo = $workshopRepo;
    }

    public function getAllWorkshops()
    {
        return $this->workshopRepo->getAll();
    }

    public function getUpcomingWorkshops()
    {
        return $this->workshopRepo->getUpcoming();
    }

    public function getUserWorkshops($userId)
    {
        return $this->workshopRepo->getUserWorkshops($userId);
    }

    public function registerForWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            // Check if already registered
            if ($this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Already registered for this workshop');
            }

            // Check capacity
            $workshop = $this->workshopRepo->find($workshopId);
            if ($workshop->current_participants >= $workshop->max_participants) {
                throw new \Exception('Workshop is full');
            }

            return $this->workshopRepo->registerUser($userId, $workshopId);
        });
    }

    public function unregisterFromWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            if (!$this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Not registered for this workshop');
            }

            return $this->workshopRepo->unregisterUser($userId, $workshopId);
        });
    }
}
