<?php

namespace Database\Seeders;

use App\Models\ClientTestimonials;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTestmonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClientTestimonials::truncate();
        $data = [
            [
                "name"=>"John Doe",
                "position"=>"Vice President | Real Estate company",
                "comment"=>"Lorem ipsum dolor sit amet,consectetur adipiscing elit. Quisqueac magna eget massa ultricies
                fringilla. Nulla vel cursus elit. Nullamrhoncus, purus ac ultrices consectetur, ipsum ipsum maximus",
                "stars"=>"5",
                "orders"=>"1",
                "file"=>"CT/client1.png"
            ],
            [
                "name"=>"Robert Smith",
                "position"=>"CEO | Flipkart company",
                "comment"=>"Lorem ipsum dolor sit amet,consectetur adipiscing elit. Quisqueac magna eget massa ultricies
                fringilla. Nulla vel cursus elit. Nullamrhoncus, purus ac ultrices consectetur, ipsum ipsum maximus",
                "stars"=>"5",
                "orders"=>"2",
                "file"=>"CT/client2.png"
            ],
            [
                "name"=>"David Cockburn",
                "position"=>"Sr Sales Manager | Amazon company",
                "comment"=>"Lorem ipsum dolor sit amet,consectetur adipiscing elit. Quisqueac magna eget massa ultricies
                fringilla. Nulla vel cursus elit. Nullamrhoncus, purus ac ultrices consectetur, ipsum ipsum maximus",
                "stars"=>"5",
                "orders"=>"3",
                "file"=>"CT/client3.png"
            ],
        ];
        foreach ($data as $value){
            ClientTestimonials::insert($value);
        }
    }
}
