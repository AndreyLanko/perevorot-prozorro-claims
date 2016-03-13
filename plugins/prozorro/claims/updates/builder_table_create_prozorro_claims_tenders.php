<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateProzorroClaimsTenders extends Migration
{
    public function up()
    {
        Schema::create('prozorro_claims_tenders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('tender_id');
            $table->text('json');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('prozorro_claims_tenders');
    }
}
