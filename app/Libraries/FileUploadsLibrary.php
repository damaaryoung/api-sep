<?php

namespace App\Libraries;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileUploadsLibrary
{
    /** @var string|null */
    protected $lastSavedPath = null;

    /** @var string|null */
    protected $lastError = null;

    /**
     * Simpan gambar dari base64 murni ke public/img.
     * - Hanya terima image: jpeg, png, webp
     * - Maksimal ukuran setelah decode: 2 MB
     * - Return true jika sukses, false jika gagal
     *
     * @param string $inputBase64 Base64 murni (tanpa "data:image/...;base64,")
     * @return bool
     */

    public function saveBase64ImageToPublicImg(string $inputBase64)
    {
        $this->lastSavedPath = null;
        $this->lastError     = null;

        if ($inputBase64 === '' || $inputBase64 === null) {
            $this->lastError = 'Base64 kosong.';
            return false;
        }

        // Decode ketat
        $binary = base64_decode($inputBase64, true);
        if ($binary === false) {
            $this->lastError = 'Base64 tidak valid.';
            return false;
        }

        // Batas ukuran 2 MB
        if (strlen($binary) > 2 * 1024 * 1024) {
            $this->lastError = 'Ukuran file melebihi 2MB.';
            return false;
        }

        // Validasi benar-benar image + deteksi mime
        $imgInfo = @getimagesizefromstring($binary);
        if ($imgInfo === false || !isset($imgInfo['mime'])) {
            $this->lastError = 'File bukan gambar yang valid.';
            return false;
        }

        $mime = strtolower($imgInfo['mime']); // ex: image/jpeg
        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!array_key_exists($mime, $extMap)) {
            $this->lastError = "Format tidak didukung: {$mime}. Hanya jpeg, png, webp.";
            return false;
        }
        $ext = $extMap[$mime];

        // Pastikan folder public/img tersedia
        $destDir = public_path('img');
        if (!File::exists($destDir)) {
            if (!File::makeDirectory($destDir, 0755, true, true)) {
                $this->lastError = 'Gagal membuat folder penyimpanan.';
                return false;
            }
        }

        // Nama file unik
        $filename = (string) Str::uuid() . '.' . $ext;
        $fullPath = $destDir . DIRECTORY_SEPARATOR . $filename;

        // Tulis file ke disk
        $bytes = @file_put_contents($fullPath, $binary);
        if ($bytes === false) {
            $this->lastError = 'Gagal menulis file ke disk.';
            return false;
        }

        // Simpan path relatif untuk dipakai aplikasi
        $this->lastSavedPath = 'img/' . $filename;
        return $filename;
    }

    public function saveBase64PdfToPublicDocuments(string $inputBase64)
    {
        $this->lastSavedPath = null;
        $this->lastError     = null;

        if ($inputBase64 === '' || $inputBase64 === null) {
            $this->lastError = 'Base64 kosong.';
            return false;
        }

        $binary = base64_decode($inputBase64, true);
        if ($binary === false) {
            $this->lastError = 'Base64 tidak valid.';
            return false;
        }

        if (strlen($binary) > 5 * 1024 * 1024) { // 5 MB
            $this->lastError = 'Ukuran file melebihi 5MB.';
            return false;
        }

        // Validasi header PDF (magic number %PDF)
        if (substr($binary, 0, 4) !== '%PDF') {
            $this->lastError = 'File bukan PDF valid.';
            return false;
        }

        $destDir = public_path('documents');
        if (!File::exists($destDir)) {
            if (!File::makeDirectory($destDir, 0755, true, true)) {
                $this->lastError = 'Gagal membuat folder penyimpanan.';
                return false;
            }
        }

        $filename = (string) Str::uuid() . '.pdf';
        $fullPath = $destDir . DIRECTORY_SEPARATOR . $filename;

        if (@file_put_contents($fullPath, $binary) === false) {
            $this->lastError = 'Gagal menulis file PDF ke disk.';
            return false;
        }

        $this->lastSavedPath = 'documents/' . $filename;
        return $filename;
    }

    /**
     * Ambil path relatif terakhir yang disimpan, mis: "img/abc-123.png"
     */
    public function getLastSavedPath(): ?string
    {
        return $this->lastSavedPath;
    }

    /**
     * Ambil pesan error terakhir jika ada
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
