<?php

namespace Database\Seeders;

use App\Models\CareerJobExperience;
use App\Models\CareerJobLocation;
use App\Models\CareerJobSalary;
use App\Models\CareerJobSkill;
use App\Models\CareerJobTitle;
use Illuminate\Database\Seeder;

class CareerJobMasterSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Job Titles
        |--------------------------------------------------------------------------
        */
        $titles = array_unique([
            'Social Media',
            'Video Editor',
            'Content Creator',
            'Video Editor Intern',
            'Graphic Designer',
            'Analyst',
            'Product Designer',
            'Content Strategist',
            'Chatbot Developer',
            'Producer',
            'Brand Manager',
            'Brand Strategist',
            'Marketing Manager',
            'Social Media Marketing Manager',
            'Media & Graphics Design Analyst',
            'Sr Manager/Manager - Influencer Marketing',
            'Digital Ads Campaign Operations Lead',
            'Sales Executive',
            'Business Development Executive',
            'Social Media Manager',
            'Google Ads Expert',
            'Marketing Manager (B2C)',
            'Content Editor',
            'Executive',
            'Document Controller',
            'Administrative Officer',
            'Document Control Analyst',
            'Executive Assistant to CPO',
            'UI/UX Designer',
        ]);

        foreach ($titles as $title) {
            CareerJobTitle::firstOrCreate([
                'name' => trim($title),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Experiences
        |--------------------------------------------------------------------------
        */
        $experiences = array_unique([
            'Fresher',
            '1 Year',
            '2 Years',
            '3 Years',
            '4 Years',
            '5 Years',
            '6 Years',
            '7 Years',
            '8 Years',
            '9 Years',
            '10+ Years',
        ]);

        foreach ($experiences as $experience) {
            CareerJobExperience::firstOrCreate([
                'name' => trim($experience),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Locations
        |--------------------------------------------------------------------------
        */
        $locations = array_unique([
            'Remote',
            'Delhi',
            'Chandigarh',
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Manipur',
            'Meghalaya',
            'Mizoram',
            'Nagaland',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Sikkim',
            'Tamil Nadu',
            'Telangana',
            'Tripura',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
            'Andaman and Nicobar Islands',
            'Dadra and Nagar Haveli and Daman and Diu',
            'Jammu and Kashmir',
            'Ladakh',
            'Lakshadweep',
            'Puducherry',
        ]);

        foreach ($locations as $location) {
            CareerJobLocation::firstOrCreate([
                'name' => trim($location),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Salaries
        |--------------------------------------------------------------------------
        */
        $salaries = array_unique([
            '1 - 3 LPA',
            '3 - 5 LPA',
            '5 - 8 LPA',
            '8 - 12 LPA',
            '12 - 20 LPA',
            '20 - 35 LPA',
            '35 - 50 LPA',
            '50+ LPA',
        ]);

        foreach ($salaries as $salary) {
            CareerJobSalary::firstOrCreate([
                'name' => trim($salary),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Skills
        |--------------------------------------------------------------------------
        */
        $skills = array_unique([
            'Adobe',
            'Adobe Creative Suite',
            'Photoshop',
            'Illustrator',
            'InDesign',
            'Figma',
            'Canva',
            'Adobe Express',
            'Instagram Stories',
            'Carousels',
            'Reels Covers',
            'Automotive Campaigns',
            'AI Tools',
            'ChatGPT',
            'Content Creation',
            'Communication Skills',
            'Research',
            'Marketing',
            'Social Media',
            'HR Operations',
            'Business Partnership',
            'Workforce Planning',
            'Leadership',
            'HR Strategy',
        ]);

        foreach ($skills as $skill) {
            CareerJobSkill::firstOrCreate([
                'name' => trim($skill),
            ]);
        }
    }
}
