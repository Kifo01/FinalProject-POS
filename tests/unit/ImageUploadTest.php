<?php
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\HTTP\Files\UploadedFile;
use App\Services\ImageUpload;
final class ImageUploadTest extends CIUnitTestCase {
    public function testMisleadingJpgFilenameIsRejectedByMime(): void {
        $path=tempnam(sys_get_temp_dir(),'pos');
        file_put_contents($path,'<?php echo "unsafe";');
        try {
            $file=new class($path,'photo.jpg','image/jpeg',filesize($path),UPLOAD_ERR_OK) extends UploadedFile { public function isValid(): bool { return true; } };
            $this->expectException(\RuntimeException::class);
            ImageUpload::store($file,'products');
        } finally { @unlink($path); }
    }
    public function testValidImageIsPreparedAsWebp(): void {
        $path=tempnam(sys_get_temp_dir(),'pos');
        $image=imagecreatetruecolor(20,20); imagefill($image,0,0,imagecolorallocate($image,230,90,60)); imagepng($image,$path); imagedestroy($image);
        $stored=null;
        try {
            $file=new class($path,'photo.png','image/png',filesize($path),UPLOAD_ERR_OK) extends UploadedFile { public function isValid(): bool { return true; } };
            $stored=ImageUpload::store($file,'products');
            $this->assertStringEndsWith('.webp',$stored);
            $this->assertSame([800,600],array_slice(getimagesize(FCPATH.$stored),0,2));
        } finally { @unlink($path); if ($stored) @unlink(FCPATH.$stored); }
    }
}
