<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims4 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->text('file')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropColumn('file');
        });
    }
}
