<?php

namespace app\models;

use Yii;
use app\models\ImageUpload;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $date
 * @property string $image
 * @property integer $viewed
 * @property integer $user_id
 * @property integer $status
 * @property integer $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title','description','content'], 'string'], 
            [['date'], 'date','format' => 'php:Y-m-d'],
            [['date'], 'default','value' => date('Y-m-d')], 
            [['title'], 'string', 'max' => 255],            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user_id' => 'User ID',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }
    
    public function saveCategory($category_id)
    {        
        $category = Category::findOne($category_id);
        if($category_id != NULL){
            $this->link('category', $category);
            return TRUE;
        }            
    }  
    
    

    public function saveTags($tags){   

        if(is_array($tags)){
            $this->clearCurrentTags();
            foreach ($tags as $tag_id){
                $tag = Tag::findOne($tag_id);
                $this->link('tags', $tag);           
            }
        }           
    } 
    
    public function clearCurrentTags() {
        ArticleTag::deleteAll(['article_id' => $this->id]);        
    }

    
    public function saveImage($filename)
    {
        $this->image = $filename;
        return $this->save(false);
    }
    
    public function deleteImage() {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }
    
    public function beforeDelete() {
        $this->deleteImage();
        parent::beforeDelete();
    }
    
    public function getImage(){      
        return ($this->image)? '/uploads/' . $this->image : '/uploads/' . 'no-image.png';
    }
    
    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    
    public function getTags() {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->
                        viaTable('article_tag', ['article_id' => 'id']);
    }

    public function getSelectedTags() {
        $selectedTegs = $this->getTags()->select('id')->asArray()->all();
        return ArrayHelper::getColumn($selectedTegs, 'id');
    }
    
    public function getDate(){
        return Yii::$app->formatter->asDate($this->date);
    }
    
    public static function getAll($pageSize = 5) {
        // build a DB query to get all articles with status = 1
        $query = Article::find();

        // get the total number of articles (but do not fetch the article data yet)
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);

        // limit the query using the pagination and retrieve the articles
        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all(); 
        
        return array('articles' => $articles,'pagination' => $pagination);         
    }
    
    public static function getRecent($limit = 5) {
        return Article::find()->orderBy('date asc')->limit($limit)->all();
    }
    
    public static function getPopular($limit = 5) {
        return Article::find()->orderBy('viewed desc')->limit($limit)->all();
    }
    
    public function saveArticle() {
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }
}