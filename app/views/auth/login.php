<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-dark text-white">Iniciar Sesión</div>
        <div class="card-body">
          <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
          <form method="POST" action="/public/index.php?module=auth&action=doLogin">
            <div class="mb-3"><label>Usuario</label><input class="form-control" name="usuario" required></div>
            <div class="mb-3"><label>Contraseña</label><input type="password" class="form-control" name="password" required></div>
            <button class="btn btn-dark w-100">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>
