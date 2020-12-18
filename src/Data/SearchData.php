<?php


namespace App\Data;


class SearchData
{
    public $q = '';
    public $rating;

    public function __toString(): string
    {
        return $this->q;
    }
}
