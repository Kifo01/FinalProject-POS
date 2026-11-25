<?php
namespace App\Controllers;
use App\Models\{ProductModel,CustomerModel,SaleModel};
use App\Services\SaleService;
class Sales extends BaseController {
    public function terminal() {
        return view('sales/terminal',['products'=>(new ProductModel())->where('active',1)->orderBy('name')->findAll(),'customers'=>(new CustomerModel())->where('active',1)->orderBy('full_name')->findAll()]);
    }
    public function record() {
        $pid=(string)$this->request->getPost('product_id'); $qty=(string)$this->request->getPost('quantity'); $cid=(string)$this->request->getPost('customer_id');
        if (!ctype_digit($pid) || !ctype_digit($qty) || (int)$qty<1 || (int)$qty>2147483647 || ($cid!=='' && !ctype_digit($cid))) return redirect()->back()->with('error','Choose a product and enter a positive whole-number quantity.');
        try {
            $id=(new SaleService())->record((int)$pid,$cid===''?null:(int)$cid,(int)session('user_id'),(int)$qty);
            return redirect()->to('/terminal')->with('success','Sale #'.$id.' recorded. Stock updated.');
        } catch (\DomainException $e) { return redirect()->back()->with('error',$e->getMessage()); }
        catch (\Throwable $e) { log_message('error','Sale failed: '.$e->getMessage()); return redirect()->back()->with('error','Sale could not be recorded. Please try again.'); }
    }
    public function history() {
        $rows=(new SaleModel())->select('sales.*, products.name AS product_name, customers.full_name AS customer_name, users.full_name AS staff_name')->join('products','products.id=sales.product_id')->join('customers','customers.id=sales.customer_id','left')->join('users','users.id=sales.sold_by')->orderBy('sales.id','DESC')->findAll();
        return view('sales/history',['rows'=>$rows]);
    }
}
