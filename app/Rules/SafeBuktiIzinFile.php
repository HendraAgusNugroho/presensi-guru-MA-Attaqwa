<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeBuktiIzinFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $mime = $value->getMimeType();
        $ext  = strtolower($value->getClientOriginalExtension());

        if ($ext === 'pdf') {
            if ($mime !== 'application/pdf') {
                $fail('File PDF tidak valid.');
            }

            return;
        }

        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            $fail('Bukti harus berupa gambar JPG/PNG atau file PDF yang valid.');
        }
    }
}
