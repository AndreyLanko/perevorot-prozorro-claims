<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims3 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dateTime('date_escalated');
            $table->string('title', 255);
            $table->text('description');
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropColumn('date_escalated');
            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }
}
