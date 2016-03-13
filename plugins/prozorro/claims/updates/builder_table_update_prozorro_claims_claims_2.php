<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateProzorroClaimsClaims2 extends Migration
{
    public function up()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropPrimary(['id']);
            $table->string('procuring_entity', 255);
            $table->string('author', 255);
            $table->string('status', 255);
        });
    }
    
    public function down()
    {
        Schema::table('prozorro_claims_claims', function($table)
        {
            $table->dropColumn('procuring_entity');
            $table->dropColumn('author');
            $table->dropColumn('status');
            $table->primary(['id']);
        });
    }
}
