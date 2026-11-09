<?php

namespace Database\Seeders;

use App\Models\Organizer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $ancienneBelgique = Organizer::create([
            'name' => 'Ancienne Belgique',
            'payment_provider' => 'mollie',
        ]);

        $ancienneBelgique->tickets()->createMany([
            [
                'title' => 'Deftones',
                'image' => 'https://consequenceofsound.net/wp-content/uploads/2019/06/Deftones.png',
                'price' => 4900,
            ],
            [
                'title' => 'Converge',
                'image' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800',
                'price' => 999,
            ],
            [
                'title' => 'At The Drive-In',
                'image' => 'https://www.laut.de/At-The-Drive-In/at-the-drive-in-182672.jpg',
                'price' => 7949,
            ],
        ]);

        $trix = Organizer::create([
            'name' => 'Trix',
            'payment_provider' => 'stripe',
        ]);

        $trix->tickets()->createMany([
            [
                'title' => 'Tool',
                'image' => 'https://cdn.wegow.com/media/artists/tool/tool-1585221549.3284898.jpg',
                'price' => 6500,
            ],
            [
                'title' => 'Amenra',
                'image' => 'https://www.thesleepingshaman.com/wp-content/uploads/2014/05/Amenra-800.jpg',
                'price' => 1999,
            ],
            [
                'title' => 'Gojira',
                'image' => 'https://images.sk-static.com/images/media/img/col6/20170608-182659-737047.jpg',
                'price' => 4945,
            ],

        ]);

        $kavka = Organizer::create([
            'name' => 'Kavka',
            'payment_provider' => 'free',
        ]);

        $kavka->tickets()->createMany([
            [
                'title' => 'La Dispute',
                'image' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=800',
                'price' => 5500,
            ],
            [
                'title' => 'Lamb of God',
                'image' => 'https://counteract.co/wp-content/uploads/2019/10/Lamb-of-God-21-800x445.jpg',
                'price' => 2999,
            ],
            [
                'title' => 'Meshuggah',
                'image' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=800',
                'price' => 3999,
            ],
        ]);
    }
}
