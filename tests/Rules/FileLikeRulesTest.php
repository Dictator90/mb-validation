<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class FileLikeRulesTest extends ValidationTestCase
{
    public function test_max_passes_for_file_like_value(): void
    {
        $file = new FileLikeValue(size: 1024, path: '/tmp/file.jpg');

        $this->assertPasses(['avatar' => $file], ['avatar' => 'max:2']);
    }

    public function test_min_fails_for_small_file_like_value(): void
    {
        $file = new FileLikeValue(size: 1024, path: '/tmp/file.jpg');

        $this->assertFails(['avatar' => $file], ['avatar' => 'min:2']);
    }

    public function test_mimes_passes_for_file_like_value(): void
    {
        $file = new FileLikeValue(
            size: 1024,
            path: '/tmp/file.jpg',
            mimeType: 'image/jpeg',
            guessedExtension: 'jpg',
            originalExtension: 'jpg'
        );

        $validator = $this->factory->make(['avatar' => $file], []);
        $this->assertTrue($validator->validateMimes('avatar', $file, ['jpg', 'png']));
    }

    public function test_mimetypes_passes_for_file_like_value(): void
    {
        $file = new FileLikeValue(
            size: 1024,
            path: '/tmp/file.jpg',
            mimeType: 'image/jpeg',
            guessedExtension: 'jpg',
            originalExtension: 'jpg'
        );

        $validator = $this->factory->make(['avatar' => $file], []);
        $this->assertTrue($validator->validateMimetypes('avatar', $file, ['image/jpeg', 'image/png']));
    }

    public function test_mimes_blocks_php_upload_by_original_extension(): void
    {
        $file = new FileLikeValue(
            size: 1024,
            path: '/tmp/file.jpg',
            mimeType: 'image/jpeg',
            guessedExtension: 'jpg',
            originalExtension: 'php'
        );

        $validator = $this->factory->make(['avatar' => $file], []);
        $this->assertFalse($validator->validateMimes('avatar', $file, ['jpg', 'png']));
    }

    public function test_invalid_uploaded_file_like_fails_max_validation(): void
    {
        $file = new FileLikeValue(size: 1024, path: '/tmp/file.jpg', valid: false);

        $this->assertFails(['avatar' => $file], ['avatar' => 'max:2']);
    }
}

class FileLikeValue
{
    public function __construct(
        private readonly int $size,
        private readonly string $path,
        private readonly string $mimeType = 'application/octet-stream',
        private readonly string $guessedExtension = 'bin',
        private readonly string $originalExtension = 'bin',
        private readonly bool $valid = true,
    ) {
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRealPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function guessExtension(): string
    {
        return $this->guessedExtension;
    }

    public function getExtension(): string
    {
        return $this->guessedExtension;
    }

    public function getClientOriginalExtension(): string
    {
        return $this->originalExtension;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }
}
