<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('email_templates')->insert([
            'key' => 'contact_reply',
            'name' => 'Contact Inquiry Reply (Admin to User)',
            'subject' => 'Re: {{subject}} — {{site_name}} Support',
            'body' => '<p>Hi <strong>{{user_name}}</strong>,</p><p>Thank you for your patience. This is a follow-up response regarding your inquiry: <strong>"{{subject}}"</strong>.</p><div style="background:#f8fafc; border-left:4px solid #6366f1; border-radius:8px; padding:20px; margin:24px 0;"><p style="margin:0; color:#334155; line-height:1.6;">{{reply_message}}</p></div><p>We hope this addresses your concerns. If you have any further questions, simply reply to this email or visit our website.</p><p>Best regards,<br><strong>The {{site_name}} Support Team</strong></p>',
            'variables' => json_encode(['user_name', 'reply_message', 'site_name', 'subject']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')->where('key', 'contact_reply')->delete();
    }
};
