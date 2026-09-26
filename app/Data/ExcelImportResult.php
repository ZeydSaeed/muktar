<?php

namespace App\Data;

readonly class ExcelImportResult
{
    /**
     * @param  list<string>  $errors
     */
    public function __construct(
        public int $families,
        public int $children,
        public array $errors,
    ) {}

    public function toastType(): string
    {
        if ($this->families === 0 && $this->children === 0) {
            return 'error';
        }

        return $this->errors === [] ? 'success' : 'warning';
    }

    public function toastMessage(): string
    {
        if ($this->families === 0 && $this->children === 0) {
            return $this->errors[0] ?? 'تعذر استيراد الملف. راجع صف العناوين وترتيب صفوف العائلة.';
        }

        $message = "تم استيراد {$this->families} عائلة و{$this->children} ابناً.";

        if ($this->errors !== []) {
            $message .= ' لم تُستورد بعض الصفوف: '.implode(' ', array_slice($this->errors, 0, 5));
        }

        return $message;
    }
}
