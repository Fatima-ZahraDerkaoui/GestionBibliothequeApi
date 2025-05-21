<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Book;
use App\Models\Purchase;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $book = Book::first();

        if ($user && $book) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Nouveau livre ajouté',
                'message' => 'Le livre "' . $book->title . '" est maintenant disponible.',
                'type' => 'new_book', // ✅ Ajouté
                'is_read' => false,
                'book_id' => $book->id,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Achat de livre',
                'message' => 'Vous avez acheté le livre "' . $book->title . '". Merci !',
                'type' => 'purchase', // ✅ Ajouté
                'is_read' => false,
                'book_id' => $book->id,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Lecture en cours',
                'message' => 'Vous avez commencé à lire "' . $book->title . '". Bonne lecture !',
                'type' => 'reading', // ✅ Ajouté
                'is_read' => false,
                'book_id' => $book->id,
            ]);
        }
    }
}