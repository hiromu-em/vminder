<?php
declare(strict_types=1);

namespace Controller;

use Core\ViewRenderer;

class DashboardController
{
    public function showDashboard(ViewRenderer $viewRenderer){
        $viewRenderer->render('dashboard');
    }
}