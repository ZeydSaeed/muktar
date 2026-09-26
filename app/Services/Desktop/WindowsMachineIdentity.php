<?php

namespace App\Services\Desktop;

use App\Exceptions\DesktopLicenseException;

class WindowsMachineIdentity implements MachineIdentity
{
    public function processorId(): string
    {
        $processorId = $this->queryProcessorId();

        if ($processorId === '') {
            throw new DesktopLicenseException('تعذر قراءة معرّف المعالج على هذه الحاسبة.');
        }

        return $processorId;
    }

    private function queryProcessorId(): string
    {
        $command = 'powershell -NoProfile -NonInteractive -Command "(Get-CimInstance -ClassName Win32_Processor | Select-Object -First 1 -ExpandProperty ProcessorId)"';
        $lines = [];
        $code = 0;
        exec($command.' 2>NUL', $lines, $code);

        if ($code !== 0) {
            return '';
        }

        foreach ($lines as $line) {
            $value = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string) $line) ?? '');
            if ($value !== '' && strlen($value) >= 8) {
                return $value;
            }
        }

        return '';
    }
}
