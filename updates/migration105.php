<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class Migration105 extends Migration
{
    public function up()
    {
        Schema::create('icecollection_icenews_post_tag', function($table)
        {
            $table->integer('post_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['post_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::drop('icecollection_icenews_post_tag');
    }
}