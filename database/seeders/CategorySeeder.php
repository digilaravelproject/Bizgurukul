<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Development',
                'sub' => ['Web Development', 'Mobile Apps', 'Game Development', 'Database Design']
            ],
            [
                'name' => 'Design',
                'sub' => ['Graphic Design', 'UI/UX Design', 'Video Editing', '3D Animation']
            ],
            [
                'name' => 'Business',
                'sub' => ['Marketing', 'Entrepreneurship', 'Sales', 'Finance']
            ],
            [
                'name' => 'IT & Software',
                'sub' => ['Cyber Security', 'Cloud Computing', 'AWS', 'Ethical Hacking']
            ]
        ];

        foreach ($categories as $cat) {
            // Parent Category create ho rahi hai
            $parent = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'parent_id' => null,
                'is_active' => true,
            ]);

            // Sub-categories create ho rahi hain
            foreach ($cat['sub'] as $subName) {
                Category::create([
                    'name' => $subName,
                    'slug' => Str::slug($subName),
                    'parent_id' => $parent->id, // Parent ki ID assign ho rahi hai
                    'is_active' => true,
                ]);
            }
        }
    }
}
