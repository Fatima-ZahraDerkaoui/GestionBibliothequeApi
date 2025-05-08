<?php

namespace App\Http\Controllers\Api;

// use App\Http\Models\Rh\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\User;

class BookController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $isAdmin = $user->role === 'admin';

    if ($isAdmin) {
        // Admin voit tous les détails
        $books = Book::all();
    } else {
        // User voit seulement les informations de base + disponibilité
        $books = Book::select('id', 'title', 'author', 'cover_image', 'quantity')
            ->get()
            ->map(function ($book) {
                $book->is_available = $book->quantity > 0;
                unset($book->quantity); // Optionnel : masquer la quantité dans la réponse
                return $book;
            });
                    
    }

    return response()->json(['data' => $books]);
}


    // Méthode pour créer un nouveau livre
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'quantity' => 'required|integer',
            'pdf_file' => 'nullable|file|mimes:pdf',
            'cover_image' => 'nullable|image',
        ]);

        $data = $request->except(['pdf_file', 'cover_image']);

        if ($request->hasFile('pdf_file')) {
            $data['pdf_path'] = $request->file('pdf_file')->store('books', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book = Book::create($data);
        return response()->json($book, 201);
    }

    // Méthode pour afficher un livre spécifique
    public function show($id) {
        $book = Book::findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'admin') {
            return response()->json($book);
        }

    // Retourne les infos réduites pour user
    return response()->json([
        'id' => $book->id,
        'title' => $book->title,
        'author' => $book->author,
        'cover_image' => $book->cover_image,
        'is_available' => $book->quantity > 0,
    ]);
}


    // Méthode pour mettre à jour un livre
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string',
            'author' => 'sometimes|string',
            'quantity' => 'sometimes|integer',
            'pdf_file' => 'nullable|file|mimes:pdf',
            'cover_image' => 'nullable|image',
        ]);

        $data = $request->except(['pdf_file', 'cover_image']);

        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_path) {
                Storage::disk('public')->delete($book->pdf_path);
            }
            $data['pdf_path'] = $request->file('pdf_file')->store('books', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($data);
        return response()->json($book, 200);
    }

    // Méthode pour supprimer un livre
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->pdf_path) Storage::disk('public')->delete($book->pdf_path);
        if ($book->cover_image) Storage::disk('public')->delete($book->cover_image);

        $book->delete();
        return response()->json(['message' => 'Livre supprimé'], 200);
    }
}
