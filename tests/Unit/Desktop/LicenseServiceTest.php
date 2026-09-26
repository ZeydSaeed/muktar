<?php

namespace Tests\Unit\Desktop;

use App\Exceptions\DesktopLicenseException;
use App\Services\Desktop\LicenseService;
use App\Services\Desktop\MachineIdentity;
use Tests\Fakes\FakeMachineIdentity;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    private string $licenseFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenseFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'muktar-license-'.uniqid('', true).'.key';

        config([
            'desktop.enabled' => true,
            'desktop.license_path' => $this->licenseFile,
            'desktop.license_secret' => 'test-secret',
        ]);

        $this->app->instance(MachineIdentity::class, new FakeMachineIdentity('CPUTEST1'));
    }

    protected function tearDown(): void
    {
        if (is_file($this->licenseFile)) {
            unlink($this->licenseFile);
        }

        parent::tearDown();
    }

    public function test_verify_passes_when_license_matches_processor(): void
    {
        $licenses = $this->app->make(LicenseService::class);
        $licenses->bind();
        $licenses->verify();

        $this->assertFileExists($this->licenseFile);
    }

    public function test_verify_fails_without_a_license_file(): void
    {
        $this->expectException(DesktopLicenseException::class);

        $this->app->make(LicenseService::class)->verify();
    }

    public function test_verify_fails_when_processor_id_does_not_match(): void
    {
        $this->app->make(LicenseService::class)->bind();
        $this->app->instance(MachineIdentity::class, new FakeMachineIdentity('OTHERCPU'));

        $this->expectException(DesktopLicenseException::class);
        $this->expectExceptionMessage('مرتبطة بمعالج جهاز آخر');

        $this->app->make(LicenseService::class)->verify();
    }
}
