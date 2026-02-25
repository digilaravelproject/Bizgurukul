<?php

namespace Database\Seeders;

use App\Models\Community;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = 'Join Our Communities';
        $group2 = 'Join Our Social Media Channels';

        $items = [
            // Group 1
            ['name' => 'Telegram', 'description' => 'Join our community', 'button_text' => 'Join Now', 'group_name' => $group1, 'order_index' => 1],
            ['name' => 'Play Store', 'description' => 'Download our app', 'button_text' => 'Download App', 'group_name' => $group1, 'order_index' => 2],
            ['name' => 'App Store', 'description' => 'Download our app', 'button_text' => 'Download App', 'group_name' => $group1, 'order_index' => 3],
            ['name' => 'Partners of Skills Pehle', 'description' => 'Follow our updates', 'button_text' => 'Follow Now', 'group_name' => $group1, 'order_index' => 4],
            ['name' => 'Partners of Skills Pehle', 'description' => 'Subscribe to our channel', 'button_text' => 'Join Now', 'group_name' => $group1, 'order_index' => 5],
            ['name' => 'WhatsApp', 'description' => 'Follow our updates', 'button_text' => 'Follow Now', 'group_name' => $group1, 'order_index' => 6],
            ['name' => 'Learners\' Community', 'description' => 'Join our community', 'button_text' => 'Join Now', 'group_name' => $group1, 'order_index' => 7],

            // Group 2
            ['name' => 'Facebook', 'description' => 'Follow for updates', 'button_text' => 'Join Now', 'group_name' => $group2, 'order_index' => 8],
            ['name' => 'YouTube', 'description' => 'Subscribe to our channel', 'button_text' => 'Join Now', 'group_name' => $group2, 'order_index' => 9],
            ['name' => 'Instagram', 'description' => 'Follow for updates', 'button_text' => 'Follow Now', 'group_name' => $group2, 'order_index' => 10],
            ['name' => 'X (Twitter)', 'description' => 'Follow for updates', 'button_text' => 'Join Now', 'group_name' => $group2, 'order_index' => 11],
            ['name' => 'LinkedIn', 'description' => 'Connect with us', 'button_text' => 'Join Now', 'group_name' => $group2, 'order_index' => 12],
            ['name' => 'WhatsApp Channel', 'description' => 'Follow our updates', 'button_text' => 'Follow Now', 'group_name' => $group2, 'order_index' => 13],
        ];

        foreach ($items as $item) {
            Community::updateOrCreate(
                ['name' => $item['name'], 'group_name' => $item['group_name']],
                $item
            );
        }
    }
}
