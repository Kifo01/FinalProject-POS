<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sign in · TAMS Cafe &amp; Co.</title>
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>">
</head>
<body class="login-body">
    <div class="login-art">
        <div class="login-art-inner">
            <span class="brand-mark">T</span>
            <p>TAMS CAFE &amp; CO. / FINAL PROJECT</p>
            <h1>Every sale,<br>made simple.</h1>
            <span>Manage products, people, and transactions in one place.</span>
        </div>
    </div>
    <main class="login-main">
        <div class="login-card">
            <span class="eyebrow">WELCOME BACK</span>
            <h1>Sign in to TAMS Cafe &amp; Co.</h1>
            <p>Enter your staff credentials to continue.</p>
            <?php if (session('error')): ?><div class="alert error"><?= esc(session('error')) ?></div><?php endif ?>
            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <label>Username<input name="username" required autocomplete="username" value="<?= esc(old('username')) ?>"></label>
                <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
                <button class="btn primary full">Sign in →</button>
            </form>
            <?php if ($demoLogin): ?>
            <section class="demo-card" aria-labelledby="demo-title">
                <h2 id="demo-title">Professor / demo login</h2>
                <p>Click a value to copy it, then paste it into the sign-in form.</p>
                <div class="demo-credentials">
                    <button type="button" class="demo-copy" data-copy="<?= esc($demoLogin['username'], 'attr') ?>" data-label="Username" aria-label="Copy demo username">
                        <span class="demo-field-label">Username</span>
                        <code><?= esc($demoLogin['username']) ?></code>
                        <span class="demo-copy-action" aria-hidden="true">Copy</span>
                    </button>
                    <button type="button" class="demo-copy" data-copy="<?= esc($demoLogin['password'], 'attr') ?>" data-label="Password" aria-label="Copy demo password">
                        <span class="demo-field-label">Password</span>
                        <code><?= esc($demoLogin['password']) ?></code>
                        <span class="demo-copy-action" aria-hidden="true">Copy</span>
                    </button>
                </div>
                <p class="demo-note">For project evaluation. Use fictional data only.</p>
                <p class="demo-copy-status" role="status" aria-live="polite"></p>
            </section>
            <script>
            document.querySelectorAll('.demo-copy').forEach(button => button.addEventListener('click', async () => {
                let copied = false;
                try {
                    await navigator.clipboard.writeText(button.dataset.copy);
                    copied = true;
                } catch (_) {
                    const field = document.createElement('textarea');
                    field.value = button.dataset.copy;
                    field.style.position = 'fixed';
                    field.style.opacity = '0';
                    document.body.appendChild(field);
                    field.select();
                    copied = document.execCommand('copy');
                    field.remove();
                }
                document.querySelector('.demo-copy-status').textContent = copied
                    ? button.dataset.label + ' copied. Paste it into the form above.'
                    : 'Copy failed. Select the value and copy it manually.';
            }));
            </script>
            <?php endif ?>
        </div>
    </main>
</body>
</html>
