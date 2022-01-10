<?php

namespace App\Utils;


use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeFrontPage extends CategoryTreeAbstract
{
    public $html_1 = '<ul>';
    public $html_2 = '<li>';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a>';
    public $html_6 = '</li>';
    public $html_7 = '</ul>';

    public function getCategoryList(array $categories_array) :string
    {

        $this->categoryList .= $this->html_1;

        foreach ($categories_array as $value) {
            $catName = $value['name'];
            $url = $this->urlGenerator->generate('video_list', ['categoryname'=>$catName, 'id'=>$value['id']]);
            $this->categoryList .= $this->html_2. $this->html_3 . $url . $this->html_4 . $catName  . $this->html_5;
            if (!empty($value['children'])){
                $this->getCategoryList($value['children']);
            }
            $this->categoryList .= $this->html_6;
        }
        $this->categoryList .= $this->html_7;

        return $this->categoryList;
    }
}