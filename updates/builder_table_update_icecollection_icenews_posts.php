<?php namespace icecollection\icenews\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateIcecollectionIcenewsPosts extends Migration
{
    public function up()
    {
        Schema::table('icecollection_icenews_posts', function($table)
        {
            $table->renameColumn('descript', 'description');
        });
    }
    
    public function down()
    {
        Schema::table('icecollection_icenews_posts', function($table)
        {
            $table->renameColumn('description', 'descript');
        });
    }
}
