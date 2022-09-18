<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateIcecollectionIcenewsCategories extends Migration
{
    public function up()
    {
        Schema::create('icecollection_icenews_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 512)->nullable();
            $table->string('description', 512)->nullable();
            $table->string('keywords', 512)->nullable();
            $table->string('slug', 512)->nullable();
            $table->boolean('is_published')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('icecollection_icenews_categories');
    }
}