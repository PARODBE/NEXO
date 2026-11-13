<?php
require_once __DIR__.'/includes/bootstrap.php'; require_once __DIR__.'/includes/layout.php';
if(current_user()) redirect('dashboard.php'); $error=''; $email='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 try {
  $email=post_text('email'); $password=post_text('password');
  if(!filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($email)>190 || strlen($password)>72) throw new InvalidArgumentException('Correo o contraseña incorrectos.');
  $user=(new UserRepository(db()))->findByEmail($email);
  if(!$user || !password_verify($password,$user['password_hash'])) throw new InvalidArgumentException('Correo o contraseña incorrectos.');
  session_regenerate_id(true); $_SESSION['user']=['id'=>(int)$user['id'],'name'=>$user['name'],'email'=>$user['email']];
  flash('success','De vuelta en tu espacio de empresa.'); after_login();
 } catch(InvalidArgumentException $exception) { $error=$exception->getMessage(); }
 catch(PDOException $exception) { error_log($exception->getMessage()); $error='No se puede acceder ahora. Comprueba la conexión a MySQL.'; }
}
render_header('Entrar');
?>
<section class="auth-layout"><div class="auth-aside"><p class="eyebrow">ACCESO EMPRESAS</p><h1>Tu próximo propósito.<br><em>Todo en un espacio.</em></h1><p>Retoma tus configuraciones, combina especialistas y revisa tus solicitudes.</p></div><div class="panel auth-panel"><p class="eyebrow">ACCESO PERSONAL</p><h2>Vuelve a tu espacio.</h2><?php if($error): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif; ?><form method="post" class="stack-form" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="field"><span>Correo electrónico</span><input type="email" name="email" autocomplete="email" maxlength="190" value="<?= e($email) ?>" required></label><label class="field"><span>Contraseña</span><input type="password" name="password" autocomplete="current-password" required></label><button class="button" type="submit">Acceder a mi espacio ↗</button></form><p class="form-switch">¿Primera vez? <a href="<?= e(url('register.php')) ?>">Crear espacio de empresa</a></p></div></section>
<?php render_footer(); ?>
