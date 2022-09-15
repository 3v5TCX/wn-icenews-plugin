<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class Migration106 extends Migration
{
    public function up()
    {
        Schema::create('icecollection_icenews_post_category', function($table)
        {
            $table->integer('post_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['post_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::drop('icecollection_icenews_post_category');
    }
}