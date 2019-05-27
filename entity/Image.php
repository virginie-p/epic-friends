<?php
namespace App\entity;

class Image {

    protected   $name,
                $tmp_name,
                $type,
                $ext,
                $width,
                $height,
                $size;

    public function __construct($image) {
        $this->name = $_FILES[$image]['name'];
        $this->tmp_name = $_FILES[$image]['tmp_name'];
        $this->type = $_FILES[$image]['type'];
        $this->ext = strtolower(pathinfo($_FILES[$image]['name'], PATHINFO_EXTENSION));
        $this->size = $_FILES[$image]['size'];
    }

    public function resizeAndCompress($image, $new_width, $new_height) {
        list($width, $height) = getimagesize($_FILES[$image]['tmp_name']);
        $this->width = $width;
        $this->height = $height;
    
        if($this->ext == 'png') {
            $image_resource = imagecreatefrompng($this->tmp_name);
        }
        else {
            $image_resource = imagecreatefromjpeg($this->tmp_name);
        }

        $new_image=imagecreatetruecolor($new_width,$new_height);
        imagecopyresampled($new_image, $image_resource, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);

        imagejpeg($new_image, $this->tmp_name, 100);
    }

    public function upload($folder) {
        $image_name = time() . $this->name;
        $upload_status = move_uploaded_file($this->tmp_name, "public/images/" . $folder . "/". $image_name);

        return $upload_results=array(
            'image_name' => $image_name, 
            'upload_status' => $upload_status);
    }

    public function isExtAllowed() {
        $allowed_ext = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
       
        return !array_key_exists($this->ext, $allowed_ext);
    }

}