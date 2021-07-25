<?php

use Illuminate\Database\Seeder;
use App\Posko;

class PoskoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posko = [
            'nama' => 'Posko Bpbd Brebes',
            'jumlah_pengungsi' => 200,
            'kontak_hp' => '085159959994',
            'lokasi' => 'Brebes',
            'latitude' => '-6.892344',
            'longitude' => '109.026947'
        ];

        Posko::create($posko);
    }
}
