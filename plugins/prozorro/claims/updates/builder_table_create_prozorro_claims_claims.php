<?php namespace prozorro\Claims\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateProzorroClaimsClaims extends Migration
{
    public function up()
    {
        Schema::create('prozorro_claims_claims', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 255);
            $table->string('tender_id', 255);
            $table->integer('user_id')->unsigned();
            $table->primary(['id','tender_id','user_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('prozorro_claims_claims');
    }
}
