<?php

namespace Tests\Feature;

use Tests\TestCase;

class CalendarioCitasPageTest extends TestCase
{
    public function test_the_calendar_page_renders_its_container_and_vite_assets(): void
    {
        $this->withoutVite()->get('/citas')
            ->assertOk()
            ->assertSee('id="calendar"', false)
            ->assertSee('Calendario de citas');
    }
}
