<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ["source", "category", "author", "title", "description", "content", "url", "image_url", "published_at"];

    /** Mentioning the searchable fields for meilisearch */
    public function toSearchableArray()
    {
        return [
            "source" => $this->source,
            "category" => $this->category,
            "author" => $this->author,
            "title" => $this->title,
            "description" => $this->description,
            "content" => $this->content,
        ];
    }
}
