<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHudsonPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hudson_pedidos', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('order_id')->unique();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');            

            //$table->integer('id_pedido')->unsigned()->unique();
            //$table->foreign('id_pedido')->references('id_pedido')->on('pedido')->onDelete('cascade');

            
            $table->integer('status');
            $table->string('message');
            $table->string('orcamento')->nullable()->default(null);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hudson_pedidos');
    }
}
