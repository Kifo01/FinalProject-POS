<?php
namespace App\Controllers;
use App\Models\UserModel;
class Staff extends AdminResource {
    protected string $kind='staff'; protected string $modelClass=UserModel::class; protected array $fields=['username','full_name','role'];
    protected function validateInput(array $input, ?int $id, ?array $old): void {
        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/',$input['username'])) throw new \DomainException('Username must be 3 to 50 letters, numbers, dots, underscores, or dashes.');
        if ($input['full_name']==='' || mb_strlen($input['full_name'])>100) throw new \DomainException('Enter a full name up to 100 characters.');
        if (!in_array($input['role'],['admin','cashier'],true)) throw new \DomainException('Choose a valid role.');
        $existing=(new UserModel())->where('username',$input['username'])->first();
        if ($existing && (int)$existing['id'] !== (int)$id) throw new \DomainException('Username is already in use.');
        $password=(string)$this->request->getPost('password');
        if ((!$id || $password!=='') && strlen($password)<10) throw new \DomainException('Password must be at least 10 characters.');
        if ($id && $old['role']==='admin' && $old['active'] && $input['role']!=='admin' && (new UserModel())->where('role','admin')->where('active',1)->countAllResults()<=1) throw new \DomainException('The last active admin cannot be demoted.');
    }
}
