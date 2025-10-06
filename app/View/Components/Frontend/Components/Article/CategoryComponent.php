<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\View\Component;

class CategoryComponent extends Component
{
    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('components.category');
    }
}
