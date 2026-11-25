<?php
namespace App\Controllers;
use App\Models\CustomerModel;
class Customers extends AdminResource {
    protected string $kind='customers'; protected string $modelClass=CustomerModel::class; protected array $fields=['full_name','email','phone'];
    protected function validateInput(array $input, ?int $id, ?array $old): void {
        if ($input['full_name']==='' || mb_strlen($input['full_name'])>100) throw new \DomainException('Enter a name up to 100 characters.');
        if (!filter_var($input['email'],FILTER_VALIDATE_EMAIL) || mb_strlen($input['email'])>100) throw new \DomainException('Enter a valid email address.');
        if ($input['phone']!=='' && (!preg_match('/^[+0-9 ()-]{7,20}$/',$input['phone']))) throw new \DomainException('Enter a valid phone number.');
    }
}
