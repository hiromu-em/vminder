<?php
declare(strict_types=1);

namespace Service;

use Repository\DashboardRepository;

class DashboardService
{
    public function __construct(private DashboardRepository $dashboardRepository)
    {

    }
}