<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImageUpload extends Model{
    
    public $image;
    
    public function rules() {
        return [
            [['image'], 'required'],
            [['image'], 'file', 'extensions' => 'jpg,png'],
        ];
    }

    public function uploadFile(UploadedFile $file, $currentImage){
        
        $this->image = $file;
        
        if($this->validate()){
            
            $this->deleteCurrentImage($currentImage);
            return $this->saveImage();
                 
        }
        
        return false;
        

    }
    
    private function getFolder(){
        return Yii::getAlias('@web') . 'uploads/';
    }
    
    private function generateFileName(){
        return strtolower(md5(uniqid($this->image->baseName)).'.'.$this->image->extension);
    }
    
    public function deleteCurrentImage($currentImage){
            if($this->existFile($currentImage)){
                unlink($this->getFolder().$currentImage);
            }
    }

    private function existFile($currentImage){
        
        if(!empty($currentImage) && $currentImage != NULL){
            return file_exists($this->getFolder().$currentImage);
        }
    }
    
    private function saveImage(){
        
        $filename = $this->generateFileName();
        $this->image->saveAs($this->getFolder(). $filename);
        
        return $filename;
    }
    
}

