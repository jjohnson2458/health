<?php

namespace Tests\Unit;

use App\Models\Medication;
use App\Models\MedicationHistory;
use Core\Encryption;
use PHPUnit\Framework\TestCase;

class MedicationModelTest extends TestCase
{
    public function test_medication_model_exists(): void
    {
        $this->assertTrue(class_exists(Medication::class));
    }

    public function test_medication_history_model_exists(): void
    {
        $this->assertTrue(class_exists(MedicationHistory::class));
    }

    public function test_decrypt_medication_decrypts_fields(): void
    {
        $med = [
            'id' => 1,
            'user_id' => 1,
            'name' => Encryption::encrypt('Metformin'),
            'dosage' => Encryption::encrypt('500mg'),
            'prescriber_name' => Encryption::encrypt('Dr. Smith'),
            'discontinued_reason' => null,
            'notes' => Encryption::encrypt('Take with food'),
            'frequency' => 'twice daily',
            'status' => 'active',
        ];

        $decrypted = Medication::decryptMedication($med);

        $this->assertEquals('Metformin', $decrypted['name']);
        $this->assertEquals('500mg', $decrypted['dosage']);
        $this->assertEquals('Dr. Smith', $decrypted['prescriber_name']);
        $this->assertEquals('Take with food', $decrypted['notes']);
        $this->assertNull($decrypted['discontinued_reason']);
    }

    public function test_crud_methods_exist(): void
    {
        $this->assertTrue(method_exists(Medication::class, 'createMedication'));
        $this->assertTrue(method_exists(Medication::class, 'updateMedication'));
        $this->assertTrue(method_exists(Medication::class, 'getActiveForUser'));
        $this->assertTrue(method_exists(Medication::class, 'getAllForUser'));
        $this->assertTrue(method_exists(Medication::class, 'findForUser'));
        $this->assertTrue(method_exists(Medication::class, 'discontinue'));
        $this->assertTrue(method_exists(Medication::class, 'reactivate'));
    }

    public function test_history_log_method_exists(): void
    {
        $this->assertTrue(method_exists(MedicationHistory::class, 'log'));
        $this->assertTrue(method_exists(MedicationHistory::class, 'getForMedication'));
    }

    public function test_controller_methods_exist(): void
    {
        $controller = \App\Controllers\MedicationController::class;
        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'create'));
        $this->assertTrue(method_exists($controller, 'store'));
        $this->assertTrue(method_exists($controller, 'edit'));
        $this->assertTrue(method_exists($controller, 'update'));
        $this->assertTrue(method_exists($controller, 'discontinue'));
        $this->assertTrue(method_exists($controller, 'reactivate'));
        $this->assertTrue(method_exists($controller, 'history'));
        $this->assertTrue(method_exists($controller, 'share'));
    }
}
