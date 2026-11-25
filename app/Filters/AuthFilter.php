<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
class AuthFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $user = session('user_id') ? (new UserModel())->find(session('user_id')) : null;
        if (!$user || !$user['active']) { session()->destroy(); return redirect()->to('/login'); }
        session()->set(['role'=>$user['role'],'full_name'=>$user['full_name']]);
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
