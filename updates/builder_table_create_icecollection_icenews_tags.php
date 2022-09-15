<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableCreateIcecollectionIcenewsTags extends Migration
{
    public function up()
    {
        Schema::create('icecollection_icenews_tags', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 512)->nullable();
            $table->string('slug', 512)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('icecollection_icenews_tags');
    }
}