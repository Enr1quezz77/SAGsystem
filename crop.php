<?php
$src_img = 'img/logo.png';
$dest_img = 'img/logo.png';

$img = @imagecreatefrompng($src_img);
if (!$img) {
    echo "Could not load image. Make sure it is a valid PNG.";
    exit;
}

$w = imagesx($img);
$h = imagesy($img);
$min = min($w, $h);

$new_img = imagecreatetruecolor($min, $min);
imagesavealpha($new_img, true);
$transparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
imagefill($new_img, 0, 0, $transparent);

$cx = $min / 2;
$cy = $min / 2;
$radius = $min / 2;

for ($x = 0; $x < $min; $x++) {
    for ($y = 0; $y < $min; $y++) {
        $dx = $x - $cx + 0.5;
        $dy = $y - $cy + 0.5;
        $dist = sqrt($dx*$dx + $dy*$dy);
        
        $src_x = $x + ($w - $min) / 2;
        $src_y = $y + ($h - $min) / 2;
        $color = imagecolorat($img, $src_x, $src_y);
        
        if ($dist <= $radius - 1) {
            imagesetpixel($new_img, $x, $y, $color);
        } elseif ($dist <= $radius) {
            // Anti-aliasing edge
            $colors = imagecolorsforindex($img, $color);
            $alpha_factor = $radius - $dist; // Between 0 and 1
            $new_alpha = 127 - intval((127 - $colors['alpha']) * $alpha_factor);
            if ($new_alpha > 127) $new_alpha = 127;
            if ($new_alpha < 0) $new_alpha = 0;
            
            $new_color = imagecolorallocatealpha($new_img, $colors['red'], $colors['green'], $colors['blue'], $new_alpha);
            imagesetpixel($new_img, $x, $y, $new_color);
        }
    }
}

imagepng($new_img, $dest_img);
imagedestroy($img);
imagedestroy($new_img);
echo "Exito";
?>
