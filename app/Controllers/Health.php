<?php
namespace App\Controllers;
class Health extends BaseController { public function index() { try { db_connect()->query('SELECT 1'); return $this->response->setJSON(['status'=>'ok']); } catch (\Throwable $e) { return $this->response->setStatusCode(503)->setJSON(['status'=>'unavailable']); } } }
