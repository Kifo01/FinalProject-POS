<?php
namespace App\Controllers;
use App\Models\ProductModel;
class Products extends AdminResource {
    protected string $kind='products'; protected string $modelClass=ProductModel::class; protected array $fields=['name','price','stock_quantity'];
    protected function validateInput(array $input, ?int $id, ?array $old): void {
        if ($input['name']==='' || mb_strlen($input['name'])>100) throw new \DomainException('Enter a product name up to 100 characters.');
        if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/',$input['price'])) throw new \DomainException('Enter a valid nonnegative price.');
        if (!ctype_digit($input['stock_quantity']) || (float)$input['stock_quantity']>2147483647) throw new \DomainException('Enter a valid nonnegative stock quantity.');
    }
}
