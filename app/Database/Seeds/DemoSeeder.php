<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class DemoSeeder extends Seeder {
    public function run() {
        if (ENVIRONMENT === 'production') throw new \RuntimeException('DemoSeeder is for local development only.');
        foreach ([['demo_admin','Demo Administrator','DemoAdmin!2026','admin'],['demo_cashier','Demo Cashier','DemoCashier!2026','cashier']] as [$username,$name,$password,$role]) {
            if (!$this->db->table('users')->where('username',$username)->countAllResults()) $this->db->table('users')->insert(['username'=>$username,'full_name'=>$name,'password'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$role,'active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
        }
        foreach ([['Classic Cheeseburger',149,24,'classic-cheeseburger'],['Chicken Sandwich',129,18,'chicken-sandwich'],['Iced Latte',110,32,'iced-latte'],['Chocolate Milkshake',135,15,'chocolate-milkshake'],['Blueberry Cheesecake',165,9,'blueberry-cheesecake'],['French Fries',85,27,'french-fries'],['Iced Tea',65,40,'iced-tea'],['Apple Pie',95,12,'apple-pie']] as [$name,$price,$stock,$slug]) {
            $image='assets/'.$slug.'.svg';
            $product=$this->db->table('products')->where('name',$name)->get()->getRowArray();
            if (!$product) $this->db->table('products')->insert(['name'=>$name,'price'=>$price,'stock_quantity'=>$stock,'image'=>$image,'active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
            elseif ($product['image'] === null) $this->db->table('products')->where('id',$product['id'])->update(['image'=>$image]);
        }
    }
}
