<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims5 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->string('tid', 255);
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropColumn('tid');
        });
    }
}
