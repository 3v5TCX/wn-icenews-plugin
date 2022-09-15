<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateIcecollectionIcenewsPosts extends Migration
{
    public function up()
    {
        Schema::create('icecollection_icenews_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->nullable()->index();
            $table->text('short_story')->nullable();
            $table->text('full_story')->nullable();
            $table->string('title', 512)->nullable();
            $table->string('descript', 512)->nullable();
            $table->string('keywords', 512)->nullable();
            $table->string('slug', 512)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(1);
            $table->boolean('is_approved')->default(1);
            $table->boolean('is_fixed')->default(0);
            $table->boolean('allow_comments')->default(0);
            $table->boolean('allow_main')->default(1);
            $table->boolean('allow_global')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('icecollection_icenews_posts');
    }
}