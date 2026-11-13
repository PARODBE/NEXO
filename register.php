<?php
require_once __DIR__.'/includes/bootstrap.php'; require_once __DIR__.'/includes/layout.php';
if(current_user()) redirect('dashboard.php');
$errors=[]; $name=''; $email='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf();
 try {
  $name=normalize_text(post_text('name')); $email=post_text('email'); $password=post_text('password');
  plain_length($name,2,80,'El nombre');
  if(!filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($email)>190) throw new InvalidArgumentException('Escribe un correo electrónico válido.');
  if(mb_strlen($password)<12 || strlen($password)>72 || preg_match('/[\x00-\x1F\x7F]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/[A-Z]/',$password) || !preg_match('/[0-9]/',$password)) throw new InvalidArgumentException('Usa al menos 12 caracteres, una mayúscula, una minúscula y un número, sin caracteres de control; máximo 72 bytes.');
  $repo=new UserRepository(db());
  if($repo->findByEmail($email)) throw new InvalidArgumentException('Ya existe una cuenta con ese correo.');
  $id=$repo->create($name,$email,$password); session_regenerate_id(true);
  $_SESSION['user']=['id'=>$id,'name'=>$name,'email'=>mb_strtolower($email)];
  flash('success','Tu espacio de empresa está listo. Diseña tu primer equipo de agentes.'); after_login();
 } catch(InvalidArgumentException $error) { $errors[]=$error->getMessage(); }
 catch(PDOException $error) { error_log($error->getMessage()); $errors[]='No se pudo crear la cuenta. Comprueba MySQL o utiliza otro correo si ya está registrado.'; }
}
render_header('Crear cuenta');
?>
<section class="auth-layout"><div class="auth-aside"><p class="eyebrow">TU ESPACIO DE EMPRESA</p><h1>Un objetivo claro.<br><em>Un equipo a su altura.</em></h1><p>Diseña una combinación de agentes para las necesidades de tu empresa.</p><ul class="auth-benefits"><li>Selecciona especialistas del catálogo.</li><li>Guarda y revisa equipos en tu espacio privado.</li><li>Registra solicitudes de propuesta con contexto.</li></ul></div><div class="panel auth-panel"><p class="eyebrow">EMPIEZA AQUÍ</p><h2>Crea tu espacio.</h2><?php foreach($errors as $error): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endforeach; ?><form method="post" class="stack-form" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="field"><span>Tu nombre</span><input name="name" autocomplete="name" minlength="2" maxlength="80" value="<?= e($name) ?>" required></label><label class="field"><span>Correo electrónico</span><input name="email" type="email" autocomplete="email" maxlength="190" value="<?= e($email) ?>" required></label><label class="field"><span>Contraseña</span><input name="password" type="password" autocomplete="new-password" minlength="12" maxlength="72" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{12,72}" aria-describedby="password-help" required></label><p class="field-help" id="password-help">Al menos 12 caracteres, con mayúscula, minúscula y número. Usa datos de ejemplo para probar esta demostración.</p><button class="button" type="submit">Crear espacio de empresa ↗</button></form><p class="form-switch">¿Ya tienes cuenta? <a href="<?= e(url('login.php')) ?>">Entrar</a></p></div></section>
<?php render_footer(); ?>
