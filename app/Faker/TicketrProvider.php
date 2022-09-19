<?php

namespace App\Faker;

use Faker\Provider\Base;

class TicketrProvider extends Base
{
    protected static $bandNames = [
        'Metallica',
        'Deftones',
        'Mastodon',
        'At The Drive-In',
        'Tool',
        'Converge',
        'La Dispute',
        'Lamb of God',
        'Rage Against The Machine',
        'The War on Drugs',
        'Editors',
        'Gojira',
        'Amenra',
        'Black Breath',
        'Casey',
        'Counterparts',
        'Every Time I Die',
        'Million Dead',
        'Architects',
        'Norma Jean',
        'Trade Wind',
        'The Chariot',
        'Childish Gambino',
        'Raised Fist',
        'Incubus',
        'Boards of Canada',
        'Thrice',
        'At The Gates',
        'Threat Signal',
        'Botch',
        'And So I Watch You From Afar',
        'Varials',
        '*shels',
        'Morning Again',
        'Once ... Never Again',
    ];

    protected static $concertOrganizers = [
        'AB',
        'Trix',
        'PukkelPop',
        'Kavka',
        'Vooruit',
        'De Oude Ketel',
        'Cirque Royal',
        'Botanique',
        'Muziekodroom',
        'Graspop',
    ];

    public function bandName(): string
    {
        return static::randomElement(static::$bandNames);
    }

    public function concertOrganizer(): string
    {
        return static::randomElement(static::$concertOrganizers);
    }
}
