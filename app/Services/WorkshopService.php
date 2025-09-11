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

            // Check capacity using the workshop's own method
            $workshop = $this->workshopRepo->find($workshopId);
            if (!$workshop->canRegister()) {
                if ($workshop->isFull()) {
                    throw new \Exception('Workshop is full');
                } elseif ($workshop->registration_deadline && now()->isAfter($workshop->registration_deadline)) {
                    throw new \Exception('Registration deadline has passed');
                } elseif (!$workshop->is_active) {
                    throw new \Exception('Workshop is not active');
                } else {
                    throw new \Exception('Registration is not available for this workshop');
                }
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
