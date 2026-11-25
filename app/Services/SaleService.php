<?php
namespace App\Services;
class SaleService {
    public function record(int $productId, ?int $customerId, int $userId, int $quantity): int {
        if ($quantity < 1) throw new \DomainException('Quantity must be a positive whole number.');
        $db=db_connect(); $db->transBegin();
        try {
            $product=$db->table('products')->where('id',$productId)->where('active',1)->get()->getRowArray();
            if (!$product) throw new \DomainException('Product is unavailable.');
            if ($customerId && !$db->table('customers')->where('id',$customerId)->where('active',1)->countAllResults()) throw new \DomainException('Customer is unavailable.');
            $updated=$db->table('products')->set('stock_quantity','stock_quantity - '.$quantity,false)->where('id',$productId)->where('active',1)->where('stock_quantity >=',$quantity)->update();
            if (!$updated || $db->affectedRows() !== 1) throw new \DomainException('Insufficient stock.');
            $total=number_format((float)$product['price']*$quantity,2,'.','');
            if (!$db->table('sales')->insert(['product_id'=>$productId,'customer_id'=>$customerId,'sold_by'=>$userId,'quantity'=>$quantity,'total_price'=>$total,'created_at'=>date('Y-m-d H:i:s')])) throw new \RuntimeException('Sale could not be recorded.');
            $id=$db->insertID();
            if ($db->transStatus() === false) throw new \RuntimeException('Sale could not be recorded.');
            $db->transCommit(); return (int)$id;
        } catch (\Throwable $e) { $db->transRollback(); throw $e; }
    }
}
