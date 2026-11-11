<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Mengubah isi kolom `link` (file PDF) menjadi URL yang bisa dibuka.
 *  - "dokumen/..."  : berkas contoh di folder public/
 *  - "http(s)://..." : sudah berupa URL
 *  - lainnya        : path di disk "public" (hasil upload, storage/app/public)
 */
class LinkFile
{
    public static function url(?string $link): ?string
    {
        if (empty($link)) {
            return null;
        }
        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://')) {
            return $link;
        }
        if (str_starts_with($link, 'dokumen/')) {
            return asset($link);
        }

        return Storage::disk('public')->url($link);
    }

    /** Hapus berkas upload (berkas contoh di public/ tidak pernah dihapus). */
    public static function hapus(?string $link): void
    {
        if ($link && ! str_starts_with($link, 'dokumen/') && ! str_starts_with($link, 'http')) {
            Storage::disk('public')->delete($link);
        }
    }
}
