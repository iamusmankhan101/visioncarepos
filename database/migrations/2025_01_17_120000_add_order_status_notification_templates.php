<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\NotificationTemplate;
use App\Business;

class AddOrderStatusNotificationTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new notification templates for all existing businesses
        $businesses = Business::all();
        
        foreach ($businesses as $business) {
            // Add "Order Ready" notification template
            NotificationTemplate::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'template_for' => 'order_ready'
                ],
                [
                    'email_body' => '<p>Dear {contact_name},</p>

                        <p>Your order {invoice_number} is ready for pickup!</p>

                        <p>Total amount: {total_amount}</p>

                        <p>Please come to collect your order at your earliest convenience.</p>

                        <p>{business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, Your order {invoice_number} is ready for pickup! Please come to collect it. {business_name}',
                    'subject' => 'Order Ready - {business_name}',
                    'auto_send' => 0,
                ]
            );

            // Add "Order Delivered" notification template
            NotificationTemplate::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'template_for' => 'order_delivered'
                ],
                [
                    'email_body' => '<p>Dear {contact_name},</p>

                        <p>Your order {invoice_number} has been delivered!</p>

                        <p>Total amount: {total_amount}</p>

                        <p>Thank you for choosing us.</p>

                        <p>{business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, Your order {invoice_number} has been delivered! Thank you for choosing us. {business_name}',
                    'subject' => 'Order Delivered - {business_name}',
                    'auto_send' => 0,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the notification templates
        NotificationTemplate::where('template_for', 'order_ready')->delete();
        NotificationTemplate::where('template_for', 'order_delivered')->delete();
    }
}