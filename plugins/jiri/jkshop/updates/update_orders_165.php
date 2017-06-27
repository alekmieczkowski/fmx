<?php namespace Jiri\JKShop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateOrders165 extends Migration
{

    public function up()
    {
        Schema::table('jiri_jkshop_orders', function ($table) {
            // Order status - payment_gateways
            $table->string('security_token')->nullable();
        });
        
        // generate security_token for all old orders
        $allOrders = \Jiri\JKShop\Models\Order::get();
        foreach ($allOrders as $order) {
            $order->security_token = uniqid();
            $order->save();
        }
    }

    public function down()
    {
        Schema::table('jiri_jkshop_orders', function ($table) {
            $table->dropColumn('security_token');
        });        
    }

}
