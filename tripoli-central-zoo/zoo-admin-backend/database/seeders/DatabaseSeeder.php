<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AnimalCategory;
use App\Models\Animal;
use App\Models\FacilityType;
use App\Models\Facility;
use App\Models\Activity;
use App\Models\MapLocation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@tripolizoo.com',
        ]);

        // Animal Categories
        $mammals = AnimalCategory::create([
            'name' => 'Mammals',
            'description' => 'Warm-blooded vertebrates with fur or hair',
            'icon' => 'mammals.png',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $birds = AnimalCategory::create([
            'name' => 'Birds',
            'description' => 'Feathered, winged, egg-laying vertebrates',
            'icon' => 'birds.png',
            'display_order' => 2,
            'is_active' => true,
        ]);

        $reptiles = AnimalCategory::create([
            'name' => 'Reptiles',
            'description' => 'Cold-blooded vertebrates with scales',
            'icon' => 'reptiles.png',
            'display_order' => 3,
            'is_active' => true,
        ]);

        // Sample Animals
        $lion = Animal::create([
            'category_id' => $mammals->id,
            'name' => 'African Lion',
            'scientific_name' => 'Panthera leo',
            'description' => 'The lion is a large cat of the genus Panthera native to Africa and India. It has a muscular, broad-chested body, short, rounded head, round ears, and a hairy tuft at the end of its tail.',
            'habitat' => 'Grasslands and savannas',
            'conservation_status' => 'Vulnerable',
            'diet' => ['Large ungulates', 'Buffalo', 'Zebra', 'Wildebeest'],
            'age' => '8 years',
            'weight' => '190 kg',
            'size' => '1.2m height, 2.5m length',
            'fun_facts' => 'Lions are the only cats that live in groups called prides. A pride can have up to 30 members.',
            'feeding_times' => ['10:00 AM', '3:00 PM'],
            'is_visible' => true,
            'is_featured' => true,
            'display_order' => 1,
        ]);

        $eagle = Animal::create([
            'category_id' => $birds->id,
            'name' => 'Golden Eagle',
            'scientific_name' => 'Aquila chrysaetos',
            'description' => 'The golden eagle is one of the best-known birds of prey in the Northern Hemisphere. It is the most widely distributed species of eagle.',
            'habitat' => 'Mountains and hills',
            'conservation_status' => 'Least Concern',
            'diet' => ['Rabbits', 'Hares', 'Ground squirrels'],
            'age' => '12 years',
            'weight' => '4.5 kg',
            'size' => '0.9m length, 2.2m wingspan',
            'fun_facts' => 'Golden eagles can spot prey from over 3 kilometers away.',
            'feeding_times' => ['11:00 AM', '2:00 PM'],
            'is_visible' => true,
            'is_featured' => true,
            'display_order' => 2,
        ]);

        $python = Animal::create([
            'category_id' => $reptiles->id,
            'name' => 'Burmese Python',
            'scientific_name' => 'Python bivittatus',
            'description' => 'The Burmese python is one of the largest species of snakes. They are found throughout Southern and Southeast Asia.',
            'habitat' => 'Tropical forests near water',
            'conservation_status' => 'Vulnerable',
            'diet' => ['Mammals', 'Birds', 'Reptiles'],
            'age' => '15 years',
            'weight' => '90 kg',
            'size' => '5 meters length',
            'fun_facts' => 'Pythons can go months without eating after a large meal.',
            'feeding_times' => ['12:00 PM'],
            'is_visible' => true,
            'is_featured' => false,
            'display_order' => 3,
        ]);

        // Facility Types
        $amenities = FacilityType::create([
            'name' => 'Amenities',
            'description' => 'Essential visitor facilities',
            'icon' => 'amenities.png',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $dining = FacilityType::create([
            'name' => 'Dining',
            'description' => 'Food and beverage locations',
            'icon' => 'dining.png',
            'display_order' => 2,
            'is_active' => true,
        ]);

        // Sample Facilities
        $restaurant = Facility::create([
            'facility_type_id' => $dining->id,
            'name' => 'Safari Café',
            'description' => 'Enjoy a variety of meals and refreshments with a view of the savanna exhibit.',
            'opening_hours' => [
                'Monday' => '9:00 AM - 5:00 PM',
                'Tuesday' => '9:00 AM - 5:00 PM',
                'Wednesday' => '9:00 AM - 5:00 PM',
                'Thursday' => '9:00 AM - 5:00 PM',
                'Friday' => '9:00 AM - 6:00 PM',
                'Saturday' => '8:00 AM - 6:00 PM',
                'Sunday' => '8:00 AM - 6:00 PM',
            ],
            'contact_phone' => '+218-21-1234567',
            'contact_email' => 'cafe@tripolizoo.com',
            'amenities' => ['Outdoor seating', 'Vegetarian options', 'Kids menu'],
            'is_accessible' => true,
            'is_open' => true,
            'capacity' => 80,
            'display_order' => 1,
        ]);

        $restroom = Facility::create([
            'facility_type_id' => $amenities->id,
            'name' => 'Main Restrooms',
            'description' => 'Clean and accessible restroom facilities near the main entrance.',
            'amenities' => ['Wheelchair accessible', 'Baby changing station', 'Family restroom'],
            'is_accessible' => true,
            'is_open' => true,
            'display_order' => 2,
        ]);

        // Sample Activities
        $lionFeeding = Activity::create([
            'name' => 'Lion Feeding Experience',
            'activity_type' => 'feeding',
            'description' => 'Watch our majestic lions being fed by experienced keepers. Learn about their diet and hunting behaviors.',
            'animal_id' => $lion->id,
            'start_time' => now()->setTime(15, 0),
            'end_time' => now()->setTime(15, 30),
            'duration_minutes' => 30,
            'capacity' => 50,
            'requires_booking' => false,
            'is_active' => true,
            'display_order' => 1,
        ]);

        $eagleShow = Activity::create([
            'name' => 'Birds of Prey Flight Show',
            'activity_type' => 'show',
            'description' => 'Marvel at the incredible flight skills of eagles, hawks, and owls in this spectacular aerial display.',
            'animal_id' => $eagle->id,
            'start_time' => now()->setTime(11, 0),
            'end_time' => now()->setTime(11, 45),
            'duration_minutes' => 45,
            'capacity' => 100,
            'requires_booking' => false,
            'is_active' => true,
            'display_order' => 2,
        ]);

        $zooTour = Activity::create([
            'name' => 'Guided Zoo Tour',
            'activity_type' => 'tour',
            'description' => 'Join our expert guides for a comprehensive tour of the zoo. Learn fascinating facts about our animals and conservation efforts.',
            'start_time' => now()->setTime(10, 0),
            'end_time' => now()->setTime(12, 0),
            'duration_minutes' => 120,
            'capacity' => 25,
            'requires_booking' => true,
            'price' => 10.00,
            'is_active' => true,
            'display_order' => 3,
        ]);

        // Map Locations
        MapLocation::create([
            'name' => 'Lion Habitat',
            'location_type' => 'animal',
            'reference_id' => $lion->id,
            'coordinate_x' => 35.5,
            'coordinate_y' => 45.2,
            'map_level' => 1,
            'description' => 'Home to our pride of African lions',
            'is_interactive' => true,
        ]);

        MapLocation::create([
            'name' => 'Eagle Aviary',
            'location_type' => 'animal',
            'reference_id' => $eagle->id,
            'coordinate_x' => 60.8,
            'coordinate_y' => 30.5,
            'map_level' => 1,
            'description' => 'Large flight aviary for birds of prey',
            'is_interactive' => true,
        ]);

        MapLocation::create([
            'name' => 'Safari Café',
            'location_type' => 'facility',
            'reference_id' => $restaurant->id,
            'coordinate_x' => 50.0,
            'coordinate_y' => 70.0,
            'map_level' => 1,
            'description' => 'Main dining facility',
            'is_interactive' => true,
        ]);

        MapLocation::create([
            'name' => 'Main Entrance Restrooms',
            'location_type' => 'facility',
            'reference_id' => $restroom->id,
            'coordinate_x' => 20.0,
            'coordinate_y' => 80.0,
            'map_level' => 1,
            'description' => 'Restroom facilities near entrance',
            'is_interactive' => true,
        ]);
    }
}
