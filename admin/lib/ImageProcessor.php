<?php

declare(strict_types=1);

final class ImageProcessor
{
    private const MAX_BYTES = 10_000_000;
    private const MAX_PIXELS = 40_000_000;
    private const MAX_SIDE = 2400;

    public static function fromUpload(array $file, string $outputExtension = 'jpg'): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new InvalidArgumentException('Choisissez une image.');
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
            throw new InvalidArgumentException('Le transfert de l’image a échoué.');
        }
        if ((int) ($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new InvalidArgumentException('L’image dépasse la taille maximale de 10 Mo.');
        }
        if (!extension_loaded('gd')) {
            throw new RuntimeException('Le traitement sécurisé des images n’est pas disponible sur le serveur.');
        }

        $tmp = (string) $file['tmp_name'];
        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new InvalidArgumentException('Ce fichier n’est pas une image valide.');
        }
        [$width, $height] = $info;
        if ($width < 320 || $height < 240) {
            throw new InvalidArgumentException('Choisissez une image d’au moins 320 × 240 pixels.');
        }
        if ($width * $height > self::MAX_PIXELS) {
            throw new InvalidArgumentException('Les dimensions de cette image sont trop importantes.');
        }

        $mime = (string) ($info['mime'] ?? '');
        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png' => @imagecreatefrompng($tmp),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : false,
            default => false,
        };
        if ($source === false) {
            throw new InvalidArgumentException('Formats acceptés : JPG, PNG et WebP.');
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($tmp);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            $source = match ($orientation) {
                3 => imagerotate($source, 180, 0),
                6 => imagerotate($source, -90, 0),
                8 => imagerotate($source, 90, 0),
                default => $source,
            };
            $width = imagesx($source);
            $height = imagesy($source);
        }

        $ratio = min(1, self::MAX_SIDE / max($width, $height));
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($target === false) {
            imagedestroy($source);
            throw new RuntimeException('Impossible de préparer cette image.');
        }

        $outputExtension = strtolower($outputExtension);
        if ($outputExtension === 'png' || $outputExtension === 'webp') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefill($target, 0, 0, $transparent);
        } else {
            $white = imagecolorallocate($target, 255, 255, 255);
            imagefill($target, 0, 0, $white);
        }
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        $written = match ($outputExtension) {
            'png' => imagepng($target, null, 6),
            'webp' => function_exists('imagewebp') ? imagewebp($target, null, 88) : false,
            default => imagejpeg($target, null, 88),
        };
        $binary = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);
        if (!$written || !is_string($binary) || $binary === '') {
            throw new RuntimeException('L’image n’a pas pu être enregistrée.');
        }
        return $binary;
    }
}
