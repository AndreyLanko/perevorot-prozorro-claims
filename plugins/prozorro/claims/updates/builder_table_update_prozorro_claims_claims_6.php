<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims6 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->text('tender_title');
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropColumn('tender_title');
        });
    }
}
