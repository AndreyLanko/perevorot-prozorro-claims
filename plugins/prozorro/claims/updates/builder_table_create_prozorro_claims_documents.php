<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateProzorroClaimsDocuments extends Migration
{
    public function up()
    {
        Schema::create('prozorro_claims_documents', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('claim_id');
            $table->text('json');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('prozorro_claims_documents');
    }
}
