<?php
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
final class AccessTest extends CIUnitTestCase {
    use DatabaseTestTrait, FeatureTestTrait;
    protected $namespace='App';
    protected $refresh=true;
    private int $admin;
    private int $cashier;
    protected function setUp(): void {
        parent::setUp();
        foreach ([['admin','admin'],['cashier','cashier']] as [$username,$role]) {
            $this->db->table('users')->insert(['username'=>$username,'full_name'=>ucfirst($username),'password'=>password_hash('Password123!',PASSWORD_DEFAULT),'role'=>$role,'active'=>1,'created_at'=>date('Y-m-d H:i:s')]);
            $this->{$role}=(int)$this->db->insertID();
        }
    }
    public function testLoggedOutUserIsRedirected(): void {
        $this->withSession([])->get('/terminal')->assertRedirectTo('/login');
    }
    public function testCashierCannotOpenAdminDashboard(): void {
        $this->withSession(['user_id'=>$this->cashier,'role'=>'cashier','full_name'=>'Cashier'])->get('/admin')->assertStatus(403);
    }
    public function testAdminCanOpenDashboard(): void {
        $this->withSession(['user_id'=>$this->admin,'role'=>'admin','full_name'=>'Admin'])->get('/admin')->assertOK();
    }
    public function testCashierCanOpenTerminal(): void {
        $this->withSession(['user_id'=>$this->cashier,'role'=>'cashier','full_name'=>'Cashier'])->get('/terminal')->assertOK();
    }
    public function testValidLoginRedirectsToRoleHome(): void {
        $this->withSession([csrf_token()=>csrf_hash()])->post('/login',['username'=>'cashier','password'=>'Password123!',csrf_token()=>csrf_hash()])->assertRedirectTo('/terminal');
    }
    public function testInvalidLoginDoesNotAuthenticate(): void {
        $this->withSession([csrf_token()=>csrf_hash()])->post('/login',['username'=>'cashier','password'=>'wrong',csrf_token()=>csrf_hash()])->assertRedirect();
        $this->assertNull(session('user_id'));
    }
    public function testLastAdminCannotBeArchived(): void {
        $this->withSession(['user_id'=>$this->admin,'role'=>'admin','full_name'=>'Admin',csrf_token()=>csrf_hash()])
            ->post('/admin/staff/'.$this->admin.'/archive',[csrf_token()=>csrf_hash()])->assertRedirect();
        $this->assertSame(1,(int)$this->db->table('users')->where('id',$this->admin)->get()->getRow('active'));
    }
    public function testCashierCannotCreateProduct(): void {
        $this->withSession(['user_id'=>$this->cashier,'role'=>'cashier','full_name'=>'Cashier',csrf_token()=>csrf_hash()])
            ->post('/admin/products',['name'=>'Forbidden','price'=>'10.00','stock_quantity'=>'1',csrf_token()=>csrf_hash()])->assertStatus(403);
        $this->assertSame(0,$this->db->table('products')->countAllResults());
    }
    public function testAdminCanCreateAndArchiveProduct(): void {
        $session=['user_id'=>$this->admin,'role'=>'admin','full_name'=>'Admin',csrf_token()=>csrf_hash()];
        $this->withSession($session)->post('/admin/products',['name'=>'Fresh Tea','price'=>'35.50','stock_quantity'=>'4',csrf_token()=>csrf_hash()])->assertRedirectTo('/admin/products');
        $product=$this->db->table('products')->where('name','Fresh Tea')->get()->getRowArray();
        $this->assertSame(4,(int)$product['stock_quantity']);
        $this->withSession($session)->post('/admin/products/'.$product['id'].'/archive',[csrf_token()=>csrf_hash()])->assertRedirect();
        $this->assertSame(0,(int)$this->db->table('products')->where('id',$product['id'])->get()->getRow('active'));
    }
    public function testStaffPasswordIsHashedAndCanBeReset(): void {
        $session=['user_id'=>$this->admin,'role'=>'admin','full_name'=>'Admin',csrf_token()=>csrf_hash()];
        $this->withSession($session)->post('/admin/staff',['username'=>'newcashier','full_name'=>'New Cashier','role'=>'cashier','password'=>'InitialPass123',csrf_token()=>csrf_hash()])->assertRedirectTo('/admin/staff');
        $user=$this->db->table('users')->where('username','newcashier')->get()->getRowArray();
        $this->assertTrue(password_verify('InitialPass123',$user['password']));
        $this->withSession($session)->post('/admin/staff/'.$user['id'],['username'=>'newcashier','full_name'=>'New Cashier','role'=>'cashier','password'=>'ResetPass123',csrf_token()=>csrf_hash()])->assertRedirectTo('/admin/staff');
        $hash=$this->db->table('users')->where('id',$user['id'])->get()->getRow('password');
        $this->assertTrue(password_verify('ResetPass123',$hash));
        $this->assertFalse(password_verify('InitialPass123',$hash));
    }
    public function testLastAdminCannotBeDemoted(): void {
        $this->withSession(['user_id'=>$this->admin,'role'=>'admin','full_name'=>'Admin',csrf_token()=>csrf_hash()])
            ->post('/admin/staff/'.$this->admin,['username'=>'admin','full_name'=>'Admin','role'=>'cashier','password'=>'',csrf_token()=>csrf_hash()])->assertRedirect();
        $this->assertSame('admin',$this->db->table('users')->where('id',$this->admin)->get()->getRow('role'));
    }
}
