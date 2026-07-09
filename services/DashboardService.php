<?php
declare(strict_types=1);

final class DashboardService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function adminStats(): array
    {
        return [
            'students' => $this->count('students'),
            'staff' => $this->count('staff'),
            'courses' => $this->count('courses'),
            'revenue' => $this->sum('payments', 'amount', "status = 'paid'"),
            'expenses' => $this->sum('expenses', 'amount', "status = 'approved'"),
        ];
    }

    public function announcements(int $limit = 6): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM announcements WHERE status = ? ORDER BY published_at DESC LIMIT ' . $limit);
        $statement->execute(['published']);
        return $statement->fetchAll();
    }

    public function upcomingTimetable(?int $userId = null, string $role = 'student'): array
    {
        $sql = 'SELECT timetable.*, courses.code, courses.title, staff.staff_no
                FROM timetable
                INNER JOIN courses ON courses.id = timetable.course_id
                LEFT JOIN staff ON staff.id = timetable.staff_id
                ORDER BY FIELD(day_of_week, "Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"), start_time
                LIMIT 8';
        return $this->pdo->query($sql)->fetchAll();
    }

    private function count(string $table): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    }

    private function sum(string $table, string $column, string $where): float
    {
        return (float) $this->pdo->query("SELECT COALESCE(SUM({$column}), 0) FROM {$table} WHERE {$where}")->fetchColumn();
    }
}
