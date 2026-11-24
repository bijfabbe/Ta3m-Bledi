<?php
class TestimonialModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(string $rating = 'all'): array
    {
        $sql = 'SELECT * FROM testimonials';
        $params = [];

        if ($rating === 'low') {
            $sql .= ' WHERE rating <= 2';
        } elseif (in_array($rating, ['5', '4', '3'], true)) {
            $sql .= ' WHERE rating = ?';
            $params[] = (int)$rating;
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSatisfactionStats(): array
    {
        $rows = $this->pdo->query('SELECT rating FROM testimonials')->fetchAll();
        $total = count($rows);
        $satisfied = 0;
        $neutral = 0;
        $unsatisfied = 0;
        $sum = 0;

        foreach ($rows as $r) {
            $note = (int)$r['rating'];
            $sum += $note;
            if ($note >= 4) {
                $satisfied++;
            } elseif ($note === 3) {
                $neutral++;
            } else {
                $unsatisfied++;
            }
        }

        return [
            'total' => $total,
            'averageRating' => $total > 0 ? round($sum / $total, 1) : 0,
            'satisfactionRate' => $total > 0 ? round(($satisfied / $total) * 100) : 0,
            'satisfied' => $satisfied,
            'neutral' => $neutral,
            'unsatisfied' => $unsatisfied,
        ];
    }
}
