<?php
namespace App\Controllers;
use App\Models\{ProductModel,CustomerModel,UserModel,SaleModel};
class Dashboard extends BaseController {
    public function index() {
        $sales = new SaleModel();
        return view('dashboard', ['products'=>(new ProductModel())->where('active',1)->countAllResults(), 'customers'=>(new CustomerModel())->where('active',1)->countAllResults(), 'staff'=>(new UserModel())->where('active',1)->countAllResults(), 'revenue'=>$sales->selectSum('total_price')->first()['total_price'] ?? 0, 'recent'=>$sales->select('sales.*, products.name AS product_name')->join('products','products.id=sales.product_id')->orderBy('sales.id','DESC')->findAll(6)]);
    }
}
