<?php

namespace App\Services\Desktop;

use App\Exceptions\DesktopLicenseException;

class LicenseService
{
    public function __construct(
        private MachineIdentity $machine,
    ) {}

    public function verify(): void
    {
        if (! config('desktop.enabled')) {
            return;
        }

        $path = $this->path();

        if (! is_file($path)) {
            throw new DesktopLicenseException('هذه النسخة غير مفعّلة على هذه الحاسبة.');
        }

        $raw = file_get_contents($path);

        if (! is_string($raw) || ! str_contains($raw, "\n")) {
            throw new DesktopLicenseException('ملف الترخيص تالف ولا يمكن تشغيل البرنامج.');
        }

        [$payload, $signature] = array_pad(explode("\n", trim($raw), 2), 2, '');
        $expected = hash_hmac('sha256', $payload, $this->secret());

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            throw new DesktopLicenseException('ملف الترخيص غير صالح.');
        }

        /** @var array{processor_id?: mixed} $data */
        $data = json_decode($payload, true);

        if (! is_array($data) || ! is_string($data['processor_id'] ?? null) || $data['processor_id'] === '') {
            throw new DesktopLicenseException('ملف الترخيص غير مكتمل.');
        }

        $bound = strtoupper($data['processor_id']);
        $current = strtoupper($this->machine->processorId());

        if (! hash_equals($bound, $current)) {
            throw new DesktopLicenseException('لا يمكن تشغيل البرنامج على هذه الحاسبة. هذه النسخة مرتبطة بمعالج جهاز آخر.');
        }
    }

    public function bind(): string
    {
        $processorId = $this->machine->processorId();
        $payload = json_encode([
            'processor_id' => $processorId,
            'bound_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (! is_string($payload)) {
            throw new DesktopLicenseException('تعذر إنشاء ملف الترخيص.');
        }

        $directory = dirname($this->path());

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new DesktopLicenseException('تعذر حفظ ملف الترخيص.');
        }

        $written = file_put_contents($this->path(), $payload."\n".hash_hmac('sha256', $payload, $this->secret()));

        if ($written === false) {
            throw new DesktopLicenseException('تعذر حفظ ملف الترخيص.');
        }

        return $processorId;
    }

    public function path(): string
    {
        $configured = config('desktop.license_path');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return storage_path('app/license.key');
    }

    private function secret(): string
    {
        return (string) config('desktop.license_secret');
    }
}
