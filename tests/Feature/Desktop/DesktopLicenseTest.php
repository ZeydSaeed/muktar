<?php

namespace Tests\Feature\Desktop;

use App\Services\Desktop\LicenseService;
use App\Services\Desktop\MachineIdentity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMachineIdentity;
use Tests\TestCase;

class DesktopLicenseTest extends TestCase
{
    use RefreshDatabase;
    private string $licenseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenseFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'muktar-web-license-'.uniqid('', true).'.key';

        config([
            'desktop.enabled' => true,
            'desktop.license_path' => $this->licenseFile,
            'desktop.license_secret' => 'test-secret',
        ]);

        $this->app->instance(MachineIdentity::class, new FakeMachineIdentity('CPUWEB1'));
    }

    protected function tearDown(): void
    {
        if (is_file($this->licenseFile)) {
            unlink($this->licenseFile);
        }

        parent::tearDown();
    }

    public function test_home_is_blocked_without_a_desktop_license(): void
    {
        $this->get(route('home'))
            ->assertForbidden()
            ->assertSee('تعذر تشغيل البرنامج', false);
    }

    public function test_home_is_allowed_when_license_matches_this_processor(): void
    {
        $this->app->make(LicenseService::class)->bind();

        $this->get(route('home'))->assertOk();
    }

    public function test_home_is_blocked_when_license_belongs_to_another_processor(): void
    {
        $this->app->make(LicenseService::class)->bind();
        $this->app->instance(MachineIdentity::class, new FakeMachineIdentity('FOREIGNCPU'));

        $this->get(route('home'))
            ->assertForbidden()
            ->assertSee('مرتبطة بمعالج جهاز آخر', false);
    }
}
