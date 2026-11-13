<?php
class UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array
    {
        // Normalizamos el correo para que las mayúsculas no creen cuentas duplicadas.
        $statement = $this->pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => mb_strtolower($email)]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $password): int
    {
        $statement = $this->pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');

        // Guardamos el hash; la contraseña original nunca se escribe en la base de datos.
        $statement->execute([
            'name' => $name,
            'email' => mb_strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
