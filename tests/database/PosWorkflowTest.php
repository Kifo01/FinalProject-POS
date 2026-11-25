<?php
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\SaleService;
use App\Models\SaleModel;
final class PosWorkflowTest extends CIUnitTestCase {
    use DatabaseTestTrait;
    protected $namespace='App';
    protected $refresh=true;
    private int $product;
    private int $staff;
    protected function setUp(): void {
        parent::setUp();
        $this->db->table('products')->insert(['name'=>'Test Sandwich','price'=>'125.50','stock_quantity'=>3,'active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
        $this->product=(int)$this->db->insertID();
        $this->db->table('users')->insert(['username'=>'tester','full_name'=>'Test Cashier','password'=>password_hash('TestPassword123',PASSWORD_DEFAULT),'role'=>'cashier','active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
        $this->staff=(int)$this->db->insertID();
    }
    public function testSaleCalculatesTotalAndReducesStock(): void {
        $id=(new SaleService())->record($this->product,null,$this->staff,2);
        $sale=(new SaleModel())->find($id);
        $this->assertSame(2,(int)$sale['quantity']);
        $this->assertEquals(251.00,$sale['total_price']);
        $this->assertSame(1,(int)$this->db->table('products')->where('id',$this->product)->get()->getRow('stock_quantity'));
    }
    public function testInsufficientStockLeavesDatabaseUntouched(): void {
        try { (new SaleService())->record($this->product,null,$this->staff,4); $this->fail('Expected stock rejection.'); }
        catch (\DomainException $e) { $this->assertSame('Insufficient stock.',$e->getMessage()); }
        $this->assertSame(0,(new SaleModel())->countAllResults());
        $this->assertSame(3,(int)$this->db->table('products')->where('id',$this->product)->get()->getRow('stock_quantity'));
    }
    public function testZeroQuantityIsRejected(): void {
        $this->expectException(\DomainException::class);
        (new SaleService())->record($this->product,null,$this->staff,0);
    }
    public function testCustomerIsOptionalAndJoinShowsNamedCustomer(): void {
        $this->db->table('customers')->insert(['full_name'=>'Ana Cruz','email'=>'ana@example.test','active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
        $customer=(int)$this->db->insertID();
        (new SaleService())->record($this->product,$customer,$this->staff,1);
        $row=(new SaleModel())->select('sales.*, customers.full_name AS customer_name')->join('customers','customers.id=sales.customer_id','left')->first();
        $this->assertSame('Ana Cruz',$row['customer_name']);
    }
    public function testInvalidCustomerRollsBackStockChange(): void {
        $this->expectException(\DomainException::class);
        try { (new SaleService())->record($this->product,99999,$this->staff,1); }
        finally {
            $this->assertSame(3,(int)$this->db->table('products')->where('id',$this->product)->get()->getRow('stock_quantity'));
            $this->assertSame(0,(new SaleModel())->countAllResults());
        }
    }
    public function testFinalUnitMakesProductUnavailable(): void {
        (new SaleService())->record($this->product,null,$this->staff,3);
        $this->assertSame(0,(int)$this->db->table('products')->where('id',$this->product)->get()->getRow('stock_quantity'));
        $this->expectException(\DomainException::class);
        (new SaleService())->record($this->product,null,$this->staff,1);
    }
}
