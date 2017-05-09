<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m170425_114918_create_comment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'text' => $this->string(),
            'user_id' => $this->integer(),
            'article_id' => $this->integer(),
            'status' => $this->integer(),             
        ]);
        
        $this->createIndex('idx-post-user_id', 'comment', 'user_id');
        
        $this->addForeignKey('fk-post-user_id', 'comment', 'user_id', 'user', 'id', 'CASCADE');
        
        $this->createIndex('idx-post-article_id', 'comment', 'article_id');
        
        $this->addForeignKey('fk-post-article_id', 'comment', 'article_id', 'article', 'id', 'CASCADE');
        
    }   

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('comment');
    }
}
