<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsTenders2 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->renameColumn('tender_id', 'tid');
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->renameColumn('tid', 'tender_id');
        });
    }
}
