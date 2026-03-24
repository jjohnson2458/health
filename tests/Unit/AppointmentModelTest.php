<?php

namespace Tests\Unit;

use App\Models\Appointment;
use Core\Encryption;
use PHPUnit\Framework\TestCase;

class AppointmentModelTest extends TestCase
{
    public function test_appointment_model_exists(): void
    {
        $this->assertTrue(class_exists(Appointment::class));
    }

    public function test_decrypt_appointment_decrypts_fields(): void
    {
        $appt = [
            'id' => 1,
            'user_id' => 1,
            'provider_name' => Encryption::encrypt('Dr. Johnson'),
            'location' => Encryption::encrypt('123 Main St'),
            'notes' => Encryption::encrypt('Fasting required'),
            'appointment_date' => '2026-04-01',
            'appointment_time' => '09:00:00',
            'type' => 'checkup',
            'status' => 'scheduled',
        ];

        $decrypted = Appointment::decryptAppointment($appt);

        $this->assertEquals('Dr. Johnson', $decrypted['provider_name']);
        $this->assertEquals('123 Main St', $decrypted['location']);
        $this->assertEquals('Fasting required', $decrypted['notes']);
    }

    public function test_crud_methods_exist(): void
    {
        $this->assertTrue(method_exists(Appointment::class, 'createAppointment'));
        $this->assertTrue(method_exists(Appointment::class, 'updateAppointment'));
        $this->assertTrue(method_exists(Appointment::class, 'getUpcomingForUser'));
        $this->assertTrue(method_exists(Appointment::class, 'getPastForUser'));
        $this->assertTrue(method_exists(Appointment::class, 'getForMonth'));
        $this->assertTrue(method_exists(Appointment::class, 'findForUser'));
        $this->assertTrue(method_exists(Appointment::class, 'markCompleted'));
        $this->assertTrue(method_exists(Appointment::class, 'markCancelled'));
        $this->assertTrue(method_exists(Appointment::class, 'getRemindersToSend'));
    }

    public function test_controller_methods_exist(): void
    {
        $controller = \App\Controllers\AppointmentController::class;
        $this->assertTrue(method_exists($controller, 'index'));
        $this->assertTrue(method_exists($controller, 'calendar'));
        $this->assertTrue(method_exists($controller, 'create'));
        $this->assertTrue(method_exists($controller, 'store'));
        $this->assertTrue(method_exists($controller, 'edit'));
        $this->assertTrue(method_exists($controller, 'update'));
        $this->assertTrue(method_exists($controller, 'complete'));
        $this->assertTrue(method_exists($controller, 'cancel'));
    }
}
