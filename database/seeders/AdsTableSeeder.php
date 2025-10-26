<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Create users and products if they don't exist
        $this->createRequiredUsersAndProducts();

        // Get some users and products to associate with ads
        $users = User::limit(5)->get();
        $products = Product::limit(10)->get();

        // Define possible ad types and placements
        $types = ['banner', 'video', 'text', 'native', 'popup'];
        $placements = ['homepage', 'sidebar', 'header', 'footer', 'product-page', 'checkout', 'blog', null];
        
        // Define some targeting options
        $targetingOptions = [
            null,
            ['device' => 'mobile'],
            ['device' => 'desktop'],
            ['hours' => [9, 10, 11, 12, 13, 14, 15, 16]], // Business hours
            ['location' => 'US'],
            ['location' => 'EU'],
            ['interest' => ['sports', 'fitness']],
            ['interest' => ['technology', 'gadgets']],
        ];

        // Create 5 sample ads
        for ($i = 1; $i <= 5; $i++) {
            $startAt = rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null;
            $endAt = $startAt ? $startAt->copy()->addDays(rand(7, 60)) : null;
            
            $isActive = (bool)rand(0, 1);
            $hasProduct = $products->isNotEmpty() && rand(0, 1);
            $hasTargeting = rand(0, 1);
            
            $type = $types[array_rand($types)];
            $content = $this->generateAdContent($type);
            
            Ad::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
                'product_id' => $hasProduct && $products->isNotEmpty() ? $products->random()->id : null,
                'title' => $this->generateAdTitle($type),
                'type' => $type,
                'content' => $content,
                'link' => $this->generateAdLink($type),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'is_active' => $isActive,
                'placement' => $placements[array_rand($placements)],
                'targeting' => $hasTargeting ? $targetingOptions[array_rand($targetingOptions)] : null,
                'is_random' => (bool)rand(0, 1),
                'weight' => rand(1, 10),
                'budget' => rand(0, 1) ? rand(100, 10000) / 10 : null,
                'max_impressions' => rand(0, 1) ? rand(1000, 100000) : null,
                'max_clicks' => rand(0, 1) ? rand(100, 10000) : null,
                'created_at' => Carbon::now()->subDays(rand(0, 90)),
                'updated_at' => Carbon::now()->subDays(rand(0, 90)),
            ]);
        }

        // Create some special case ads
        $this->createSpecialCaseAds($users, $products);
    }

    private function createRequiredUsersAndProducts(): void
{
    // Create at least one user if none exist
    if (User::count() === 0) {
        User::factory()->count(5)->create();
    }

    // Skip product creation if Product model doesn't exist or table has different structure
    if (class_exists(Product::class) && Product::count() === 0) {
        try {
            // Try to create one product to test the schema
            Product::create([
                'title' => 'Sample Product', // Try 'title' first
                'description' => 'Sample description',
                'price' => 99.99,
            ]);
        } catch (\Exception $e) {
            // If that fails, try with 'name'
            try {
                Product::create([
                    'name' => 'Sample Product',
                    'description' => 'Sample description', 
                    'price' => 99.99,
                ]);
            } catch (\Exception $e) {
                // If both fail, just skip product creation
                \Log::info('Product creation skipped: ' . $e->getMessage());
            }
        }
    }
}

    private function generateAdTitle(string $type): string
    {
        $titles = [
            'banner' => [
                'Special Offer Today Only!',
                'Limited Time Discount',
                'New Collection - Shop Now',
                'Summer Sale - Up to 50% Off',
                'Exclusive Deal for You'
            ],
            'video' => [
                'Watch Our New Product Video',
                'How It Works - Video Demo',
                'Customer Stories - Video Testimonials',
                'Behind the Scenes - Video Tour',
                'Product Features Explained'
            ],
            'text' => [
                'Why You Need This Product',
                '5 Reasons to Buy Today',
                'The Science Behind Our Product',
                'Customer Reviews You Should Read',
                'Limited Stock Available'
            ],
            'native' => [
                'You Might Also Like',
                'Recommended for You',
                'Popular Among Customers',
                'Trending Now',
                'Customers Also Bought'
            ],
            'popup' => [
                'Exclusive Offer Just for You!',
                'Get 10% Off Your First Order',
                'Subscribe for Special Deals',
                'Don\'t Miss Out - Limited Time',
                'Free Shipping on Orders Over $50'
            ]
        ];

        return $titles[$type][array_rand($titles[$type])];
    }

    private function generateAdContent(string $type): ?string
    {
        if ($type === 'text') {
            $contents = [
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula.',
                'Sed auctor neque eu tellus rhoncus ut eleifend nibh porttitor. Ut in nulla enim. Phasellus molestie magna non est bibendum non venenatis nisl tempor.',
                'Praesent congue erat at massa. Sed sit amet facilisis sem. Donec luctus aliquam odio, eu gravida ipsum molestie ac.',
                'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia.',
                'Cras ultricies ligula sed magna dictum porta. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus.'
            ];
            return $contents[array_rand($contents)];
        }

        if ($type === 'banner') {
            $images = [
                'https://example.com/banners/banner1.jpg',
                'https://example.com/banners/banner2.jpg',
                'https://example.com/banners/banner3.jpg',
                'https://example.com/banners/banner4.jpg',
                'https://example.com/banners/banner5.jpg'
            ];
            return $images[array_rand($images)];
        }

        if ($type === 'video') {
            $videos = [
                'https://example.com/videos/ad1.mp4',
                'https://example.com/videos/ad2.mp4',
                'https://example.com/videos/ad3.mp4',
                'https://www.youtube.com/embed/example1',
                'https://www.youtube.com/embed/example2'
            ];
            return $videos[array_rand($videos)];
        }

        return null;
    }

    private function generateAdLink(string $type): string
    {
        $baseLinks = [
            '/special-offer',
            '/new-arrivals',
            '/clearance',
            '/limited-time',
            '/exclusive-deal',
            '/product-of-the-day',
            '/seasonal-sale'
        ];

        $queryParams = [
            'utm_source=website',
            'utm_medium=ad',
            'utm_campaign=' . $type . '_ad',
            'ref=ad_' . $type
        ];

        $randomBase = $baseLinks[array_rand($baseLinks)];
        $randomParams = $queryParams[array_rand($queryParams)];

        return $randomBase . '?' . $randomParams;
    }

    private function createSpecialCaseAds($users, $products): void
    {
        // Ad with no end date (runs indefinitely)
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'title' => 'Permanent Brand Ad',
            'type' => 'banner',
            'content' => 'https://example.com/banners/brand.jpg',
            'link' => '/about-us',
            'start_at' => Carbon::now()->subMonths(3),
            'end_at' => null,
            'is_active' => true,
            'placement' => 'header',
            'weight' => 5,
        ]);

        // Ad that hasn't started yet
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'title' => 'Upcoming Holiday Sale',
            'type' => 'banner',
            'content' => 'https://example.com/banners/holiday.jpg',
            'link' => '/holiday-sale',
            'start_at' => Carbon::now()->addDays(10),
            'end_at' => Carbon::now()->addDays(30),
            'is_active' => true,
            'placement' => 'homepage',
            'weight' => 8,
        ]);

        // Expired ad
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'product_id' => $products->isNotEmpty() ? $products->random()->id : null,
            'title' => 'Old Christmas Sale',
            'type' => 'banner',
            'content' => 'https://example.com/banners/christmas.jpg',
            'link' => '/christmas-sale',
            'start_at' => Carbon::now()->subDays(60),
            'end_at' => Carbon::now()->subDays(30),
            'is_active' => true, // Should show as expired due to date
            'placement' => 'homepage',
        ]);

        // High priority ad
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'title' => 'Important Announcement',
            'type' => 'text',
            'content' => 'We have important news about our service updates. Click to learn more.',
            'link' => '/announcements',
            'start_at' => null,
            'end_at' => null,
            'is_active' => true,
            'placement' => 'header',
            'is_random' => false,
            'weight' => 10, // Highest priority
        ]);

        // Mobile-only ad
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'product_id' => $products->isNotEmpty() ? $products->random()->id : null,
            'title' => 'Mobile App Exclusive',
            'type' => 'banner',
            'content' => 'https://example.com/banners/mobile-app.jpg',
            'link' => '/mobile-app-download',
            'start_at' => null,
            'end_at' => null,
            'is_active' => true,
            'placement' => 'sidebar',
            'targeting' => ['device' => 'mobile'],
            'weight' => 7,
        ]);

        // Business hours ad
        Ad::create([
            'user_id' => $users->isNotEmpty() ? $users->random()->id : User::first()->id,
            'title' => 'Customer Support Available',
            'type' => 'text',
            'content' => 'Our support team is available 9am-5pm. Click to chat now!',
            'link' => '/customer-support',
            'start_at' => null,
            'end_at' => null,
            'is_active' => true,
            'placement' => 'footer',
            'targeting' => ['hours' => [9, 10, 11, 12, 13, 14, 15, 16]],
        ]);
    }
}