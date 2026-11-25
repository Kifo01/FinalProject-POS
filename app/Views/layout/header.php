<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= esc($title ?? 'FinalProject POS') ?></title><link rel="stylesheet" href="<?= base_url('assets/app.css') ?>"></head><body>
<div class="app"><aside class="sidebar"><a class="brand" href="<?= site_url(session('role')==='admin'?'admin':'terminal') ?>"><span class="brand-mark">F</span><span>Folio<span class="brand-sub">POS SYSTEM</span></span></a><div class="nav-label">WORKSPACE</div><nav>
<?php if (session('role')==='admin'): ?>
<a href="<?= site_url('admin') ?>">▦ <span>Dashboard</span></a><a href="<?= site_url('admin/products') ?>">▣ <span>Products</span></a><a href="<?= site_url('admin/customers') ?>">♧ <span>Customers</span></a><a href="<?= site_url('admin/staff') ?>">♟ <span>Staff</span></a>
<?php endif ?>
<a href="<?= site_url('terminal') ?>">▤ <span>Cashier Terminal</span></a>
<?php if (session('role')==='admin'): ?><a href="<?= site_url('admin/sales') ?>">◷ <span>Sales History</span></a><?php endif ?>
</nav><div class="sidebar-bottom"><div class="user-avatar"><?= esc(strtoupper(substr((string)session('full_name'),0,1))) ?></div><div><strong><?= esc(session('full_name')) ?></strong><small><?= esc(ucfirst((string)session('role'))) ?></small></div><form action="<?= site_url('logout') ?>" method="post"><?= csrf_field() ?><button class="logout" title="Log out">↪</button></form></div></aside>
<main class="main"><header class="topbar"><div><span class="eyebrow">FINAL PROJECT / POINT OF SALE</span><h1><?= esc($title ?? 'Point of Sale') ?></h1></div><div class="topbar-right"><span class="date"><?= esc(date('l, F j, Y')) ?></span><div class="top-avatar"><?= esc(strtoupper(substr((string)session('full_name'),0,1))) ?></div></div></header>
<?php if (session('success')): ?><div class="alert success"><?= esc(session('success')) ?></div><?php endif ?>
<?php if (session('error')): ?><div class="alert error"><?= esc(session('error')) ?></div><?php endif ?>
