<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsTenders3 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->renameColumn('tid', 'id');
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->renameColumn('id', 'tid');
        });
    }
}
