<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;

class UnitController extends Controller
{
    public function toggle(): void
    {
        $current = Session::get('unit_system', 'us');
        Session::set('unit_system', $current === 'us' ? 'metric' : 'us');
        $this->back();
    }
}
