<?php

namespace Tests\Unit;

use Core\Model;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function test_paginate_method_exists_on_model(): void
    {
        $this->assertTrue(method_exists(Model::class, 'paginate'));
    }

    public function test_health_entry_paginate_for_user_method_exists(): void
    {
        $this->assertTrue(method_exists(\App\Models\HealthEntry::class, 'paginateForUser'));
    }

    public function test_export_controller_exists(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\ExportController::class));
    }

    public function test_export_controller_has_csv_method(): void
    {
        $this->assertTrue(method_exists(\App\Controllers\ExportController::class, 'csv'));
    }

    public function test_entry_controller_has_index_method(): void
    {
        $this->assertTrue(method_exists(\App\Controllers\EntryController::class, 'index'));
    }
}
