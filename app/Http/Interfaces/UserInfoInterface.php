<?php

namespace App\Http\Interfaces;

interface UserInfoInterface
{
    public function getUserInfo();
    public function update(array $data);
    public function getAllUserInfo();

}
