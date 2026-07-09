<?php
declare(strict_types=1);

final class UserService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function register(string $role, string $name, string $email, string $password, array $profile): int
    {
        $this->pdo->beginTransaction();

        $statement = $this->pdo->prepare(
            'INSERT INTO users (role_id, name, email, password, status) VALUES ((SELECT id FROM roles WHERE slug = ?), ?, ?, ?, ?)'
        );
        $statement->execute([$role, $name, $email, password_hash($password, PASSWORD_DEFAULT), 'pending']);
        $userId = (int) $this->pdo->lastInsertId();

        if ($role === 'student') {
            $profileStatement = $this->pdo->prepare(
                'INSERT INTO students (user_id, matric_no, level, phone, address) VALUES (?, ?, ?, ?, ?)'
            );
            $profileStatement->execute([
                $userId,
                $profile['matric_no'] ?? null,
                $profile['level'] ?? 'ND1',
                $profile['phone'] ?? null,
                $profile['address'] ?? null,
            ]);
        }

        if ($role === 'staff') {
            $profileStatement = $this->pdo->prepare(
                'INSERT INTO staff (user_id, staff_no, designation, phone, office) VALUES (?, ?, ?, ?, ?)'
            );
            $profileStatement->execute([
                $userId,
                $profile['staff_no'] ?? null,
                $profile['designation'] ?? 'Lecturer',
                $profile['phone'] ?? null,
                $profile['office'] ?? null,
            ]);
        }

        $this->pdo->commit();
        return $userId;
    }

    public function authenticate(string $email, string $password): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT users.*, roles.slug AS role FROM users INNER JOIN roles ON roles.id = users.role_id WHERE users.email = ? LIMIT 1'
        );
        $statement->execute([$email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }
}
