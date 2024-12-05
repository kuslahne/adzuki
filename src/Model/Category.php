<?php
declare(strict_types=1);

namespace App\Model;

class Category
{
    const MIN_CONTENT_LENGTH= 50;
    
    public int $id;
    public string $name;
    public string $description;
    public string $metaDescription;
}
