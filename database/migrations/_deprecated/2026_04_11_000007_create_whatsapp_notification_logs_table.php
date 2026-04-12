<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_phone', 20);           // +50499999999
            $table->string('template_name', 80);      // order_confirmed, delivery_assigned, etc.
            $table->json('template_params')->nullable(); // variables del template
            $table->text('message_body')->nullable();  // mensaje renderizado
            $table->string('twilio_sid', 100)->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'read'])->default('pending');
            $table->text('error_message')->nullable();

            // Polimorfismo: orden, ride, booking, service_request
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['to_phone', 'status']);
            $table->index('template_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notification_logs');
    }
};
