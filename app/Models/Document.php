<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'category_id',
        'title',
        'contents',
    ];

    // Relacionamento com a tabela categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
