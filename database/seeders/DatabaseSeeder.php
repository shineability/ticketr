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
                'image' => 'https://www.revolvermag.com/sites/default/files/styles/image_750_x_420/public/media/images/article/converge-by-david-robinson.2-b_w.jpg',
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
                'image' => 'https://media.altpress.com/uploads/2018/06/LaDisputeFINAL.jpg',
                'price' => 5500,
            ],
            [
                'title' => 'Lamb of God',
                'image' => 'https://counteract.co/wp-content/uploads/2019/10/Lamb-of-God-21-800x445.jpg',
                'price' => 2999,
            ],
            [
                'title' => 'Meshuggah',
                'image' => 'https://studiosol-a.akamaihd.net/uploadfile/letras/fotos/e/a/d/c/eadc6d479fd343f8945cf90d8ec7609f.jpg',
                'price' => 3999,
            ],
        ]);
    }
}
