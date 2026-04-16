<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beginner_guide_videos', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained('beginner_guide_categories')->onDelete('set null');
        });

        // Seed default categories
        $categories = [
            ['name' => 'Foundation', 'slug' => 'foundation', 'order_column' => 0],
            ['name' => 'Growth', 'slug' => 'growth', 'order_column' => 1],
            ['name' => 'Scale', 'slug' => 'scale', 'order_column' => 2],
        ];

        foreach ($categories as $cat) {
            $id = DB::table('beginner_guide_categories')->insertGetId(array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::table('beginner_guide_videos')
                ->where('category', $cat['slug'])
                ->update(['category_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('beginner_guide_videos', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
