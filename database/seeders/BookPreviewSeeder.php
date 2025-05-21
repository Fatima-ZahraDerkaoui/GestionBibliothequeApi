<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookPreview;
use App\Models\User;
use App\Models\Book;

class BookPreviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        foreach ($users as $user) {
            BookPreview::create([
                'user_id' => $user->id,
                'book_id' => $books->random()->id,
                'previewed_at' => now()->subDays(rand(1, 10)),
            ]);
        }
    }
}
