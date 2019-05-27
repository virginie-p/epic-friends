<?php

namespace App\Controller;
use App\Entity\Image;
require(__DIR__.'/../config.php');

class Controller {
    protected $twig;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../view');
        $twig = new \Twig\Environment($loader);

        $twig->addGlobal('BASE_URL', BASE_URL.'/');

        $this->twig = $twig;
    }

    public function createImageInFolder($image_input_name, $width, $height, $folder) {
        $image = new Image($image_input_name);
        $errors = [];

        if ($image->isExtAllowed()) {
            $upload_errors = 'invalid_extension';
        }
        else {
            $upload_errors = null;
        }

        if (is_null($upload_errors)) {
            $image->resizeAndCompress($image_input_name, $width, $height);
            $upload_result = $image->upload($folder);
        } else {
            $upload_result = null;
        }

        if (is_null($upload_errors) && $upload_result['upload_status'] == true) {
            $image_name = $upload_result['image_name'];
            
            return $upload_result = [
                "status" => "success",
                "image_name" => $image_name,
            ];
        } 
        else {

            $errors[] = $upload_data['upload_errors'];

            if (!empty($upload_results) && !$upload_results['upload_status']) {
                $errors[] = 'file_not_moved';
            }

            return $upload_result = [
                "status" => "errors",
                "errors" => $errors,
            ];
        }

        
    }
}