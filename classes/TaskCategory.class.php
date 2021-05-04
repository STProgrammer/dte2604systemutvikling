<?php


class TaskCategory
{
//    public $taskID;
    private $categoryName;


    public function __construct()
    {
    }

    public function getCategoryName(){return $this->categoryName;}

    public function setCategoryName($categoryName): void{$this->categoryName = $categoryName;}
}