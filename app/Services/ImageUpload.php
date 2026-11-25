<?php
namespace App\Services;
use CodeIgniter\HTTP\Files\UploadedFile;
class ImageUpload {
    public static function store(?UploadedFile $file, string $kind, ?string $old=null): ?string {
        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) return $old;
        if (!$file->isValid() || $file->getSize() > 2*1024*1024) throw new \RuntimeException('Image must be JPG, PNG, or WebP and under 2 MB.');
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getTempName());
        if (!in_array($mime,['image/jpeg','image/png','image/webp'],true)) throw new \RuntimeException('Image must be JPG, PNG, or WebP.');
        $source = match($mime) { 'image/jpeg'=>@imagecreatefromjpeg($file->getTempName()), 'image/png'=>@imagecreatefrompng($file->getTempName()), default=>@imagecreatefromwebp($file->getTempName()) };
        if (!$source) throw new \RuntimeException('Image could not be read.');
        $w=imagesx($source); $h=imagesy($source);
        if ($w<1 || $h<1 || $w>6000 || $h>6000) { imagedestroy($source); throw new \RuntimeException('Invalid image dimensions.'); }
        $target=imagecreatetruecolor(800,600); $bg=imagecolorallocate($target,248,247,244); imagefill($target,0,0,$bg);
        $ratio=min(800/$w,600/$h); $nw=(int)round($w*$ratio); $nh=(int)round($h*$ratio);
        imagecopyresampled($target,$source,(int)((800-$nw)/2),(int)((600-$nh)/2),0,0,$nw,$nh,$w,$h);
        $dir=FCPATH.'uploads/'.$kind; if (!is_dir($dir)) mkdir($dir,0755,true);
        $name=bin2hex(random_bytes(16)).'.webp'; imagewebp($target,$dir.'/'.$name,82); imagedestroy($target); imagedestroy($source);
        return 'uploads/'.$kind.'/'.$name;
    }
}
