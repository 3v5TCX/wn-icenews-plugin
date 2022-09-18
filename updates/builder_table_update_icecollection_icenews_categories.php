<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateIcecollectionIcenewsCategories extends Migration
{
    public function up()
    {
        Schema::rename('icecollection_icenews_categorys', 'icecollection_icenews_categories');
        Schema::table('icecollection_icenews_categories', function($table)
        {
            $table->renameColumn('descript', 'description');
        });
    }
    
    public function down()
    {
        Schema::rename('icecollection_icenews_categories', 'icecollection_icenews_categorys');
        Schema::table('icecollection_icenews_categorys', function($table)
        {
            $table->renameColumn('description', 'descript');
        });
    }
}
