<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropPrimary(['id','tender_id','user_id']);
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropPrimary(['id']);
            $table->primary(['id','tender_id','user_id']);
        });
    }
}
