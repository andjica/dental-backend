<?php
namespace App\Http\Interfaces;

interface CategoryInterface
{
    public function getAll();
    public function create(array $data);
    public function update($id, array $data);
    public function show($id);
    public function delete($id);
}