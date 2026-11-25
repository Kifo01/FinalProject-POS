<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
class AdminFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $user = session('user_id') ? (new UserModel())->find(session('user_id')) : null;
        if (!$user || !$user['active']) return redirect()->to('/login');
        if ($user['role'] !== 'admin') return service('response')->setStatusCode(403)->setBody('Access denied.');
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
