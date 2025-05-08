<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::insert([
            [
                'title' => 'Les Misérables',
                'author' => 'Victor Hugo',
                'description' => 'Un roman classique sur la misère et la rédemption.',
                'cover_image' => 'books/Petit_Prince.jpg',
                'pdf_path' => 'books/Le petit prince.pdf',
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Le Petit Prince',
                'author' => 'Antoine de Saint-Exupéry',
                'description' => 'Un conte poétique et philosophique.',
                'cover_image' => 'books/Petit_Prince.jpg',
                'pdf_path' => 'books/Le petit prince.pdf',
                'quantity' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'L\'Étranger',
                'author' => 'Albert Camus',
                'description' => 'Un roman existentialiste.',
                'cover_image' => 'books/Petit_Prince.jpg',
                'pdf_path' => 'books/Le petit prince.pdf',
                'quantity' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
    }
}
