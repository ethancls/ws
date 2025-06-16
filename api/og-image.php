<?php
// Vérifier que les headers ne sont pas déjà envoyés
if (headers_sent()) {
    exit('Headers already sent');
}

// Chemin vers l'image de fond
$backgroundPath = __DIR__ . '/../public/background.jpeg';

// Vérifier que le fichier existe
if (!file_exists($backgroundPath)) {
    // Si l'image n'existe pas, créer une image d'erreur
    header('Content-Type: image/png');
    $errorImage = imagecreatetruecolor(1200, 630);
    $backgroundColor = imagecolorallocate($errorImage, 255, 0, 0);
    $textColor = imagecolorallocate($errorImage, 255, 255, 255);
    imagefill($errorImage, 0, 0, $backgroundColor);
    imagestring($errorImage, 5, 400, 300, 'Image not found: ' . $backgroundPath, $textColor);
    imagepng($errorImage);
    imagedestroy($errorImage);
    exit;
}

// Lire l'image de fond
$imageInfo = getimagesize($backgroundPath);
if (!$imageInfo) {
    header('Content-Type: image/png');
    $errorImage = imagecreatetruecolor(1200, 630);
    $backgroundColor = imagecolorallocate($errorImage, 255, 0, 0);
    $textColor = imagecolorallocate($errorImage, 255, 255, 255);
    imagefill($errorImage, 0, 0, $backgroundColor);
    imagestring($errorImage, 5, 400, 300, 'Invalid image format', $textColor);
    imagepng($errorImage);
    imagedestroy($errorImage);
    exit;
}

// Charger l'image selon son type
$sourceImage = null;
switch ($imageInfo[2]) {
    case IMAGETYPE_JPEG:
        $sourceImage = imagecreatefromjpeg($backgroundPath);
        break;
    case IMAGETYPE_PNG:
        $sourceImage = imagecreatefrompng($backgroundPath);
        break;
    case IMAGETYPE_GIF:
        $sourceImage = imagecreatefromgif($backgroundPath);
        break;
    default:
        header('Content-Type: image/png');
        $errorImage = imagecreatetruecolor(1200, 630);
        $backgroundColor = imagecolorallocate($errorImage, 255, 0, 0);
        $textColor = imagecolorallocate($errorImage, 255, 255, 255);
        imagefill($errorImage, 0, 0, $backgroundColor);
        imagestring($errorImage, 5, 400, 300, 'Unsupported image type', $textColor);
        imagepng($errorImage);
        imagedestroy($errorImage);
        exit;
}

if (!$sourceImage) {
    header('Content-Type: image/png');
    $errorImage = imagecreatetruecolor(1200, 630);
    $backgroundColor = imagecolorallocate($errorImage, 255, 0, 0);
    $textColor = imagecolorallocate($errorImage, 255, 255, 255);
    imagefill($errorImage, 0, 0, $backgroundColor);
    imagestring($errorImage, 5, 400, 300, 'Failed to load image', $textColor);
    imagepng($errorImage);
    imagedestroy($errorImage);
    exit;
}

// Dimensions de l'image OG (standard)
$ogWidth = 1200;
$ogHeight = 630;

// Redimensionner l'image aux dimensions OG
$ogImage = imagecreatetruecolor($ogWidth, $ogHeight);
imagecopyresampled(
    $ogImage, $sourceImage,
    0, 0, 0, 0,
    $ogWidth, $ogHeight,
    imagesx($sourceImage), imagesy($sourceImage)
);

// Nettoyer l'image source
imagedestroy($sourceImage);

// Envoyer les headers appropriés
header('Content-Type: image/png');
header('Cache-Control: public, max-age=3600');
header('Access-Control-Allow-Origin: *');

// Sortir l'image
imagepng($ogImage);
imagedestroy($ogImage);
?>
