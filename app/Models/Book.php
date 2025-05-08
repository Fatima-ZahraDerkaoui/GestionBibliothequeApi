<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'author', 
        'category', 
        'description', 
        'quantity', 
        'pdf_path', 
        'cover_image'
    ];

}