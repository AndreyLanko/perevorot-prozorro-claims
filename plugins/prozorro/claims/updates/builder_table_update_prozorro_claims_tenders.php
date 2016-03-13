<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsTenders extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->integer('user_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_tenders', function($table)
        {
            $table->dropColumn('user_id');
        });
    }
}
