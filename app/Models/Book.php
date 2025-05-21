<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BookPreview;
use App\Models\Purchase;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';
    public $timestamps = false;

    protected $fillable = [
        'title', 'author', 'category', 'description', 'quantity', 'price', 'pdf_path', 'cover_image'
    ];

    public function previews() {
        return $this->hasMany(BookPreview::class);
    }

    public function purchases() {
        return $this->hasMany(Purchase::class);
    }
}