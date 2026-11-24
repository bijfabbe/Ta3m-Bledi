<?php
class VarietyModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(string $category = 'Tous', string $search = ''): array
    {
        $sql = 'SELECT * FROM varieties WHERE 1=1';
        $params = [];

        if ($category !== '' && $category !== 'Tous') {
            $sql .= ' AND cat = ?';
            $params[] = $category;
        }
        if (trim($search) !== '') {
            $sql .= ' AND (name LIKE ? OR regions LIKE ? OR description LIKE ?)';
            $like = '%' . trim($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM varieties WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO varieties (name, cat, regions, description, img, price, sales_count, stock, created_by_role)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            trim($data['name']),
            $data['cat'],
            trim($data['regions']),
            trim($data['description']),
            trim($data['img'] ?? ''),
            $data['price'],
            (int)($data['sales_count'] ?? 0),
            (int)($data['stock'] ?? 50),
            $data['created_by_role'],
        ]);
    }

    public function update(int $id, array $data, bool $updatePrice): void
    {
        if ($updatePrice) {
            $stmt = $this->pdo->prepare(
                'UPDATE varieties
                 SET name = ?, cat = ?, regions = ?, description = ?, img = ?, price = ?, sales_count = ?, stock = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                trim($data['name']),
                $data['cat'],
                trim($data['regions']),
                trim($data['description']),
                trim($data['img'] ?? ''),
                (float)$data['price'],
                (int)$data['sales_count'],
                (int)$data['stock'],
                $id,
            ]);
        } else {
            // Un agriculteur ne modifie jamais la colonne price
            $stmt = $this->pdo->prepare(
                'UPDATE varieties
                 SET name = ?, cat = ?, regions = ?, description = ?, img = ?, sales_count = ?, stock = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                trim($data['name']),
                $data['cat'],
                trim($data['regions']),
                trim($data['description']),
                trim($data['img'] ?? ''),
                (int)$data['sales_count'],
                (int)$data['stock'],
                $id,
            ]);
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM varieties WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function getSalesStats(): array
    {
        $rows = $this->pdo->query(
            'SELECT id, name, cat, price, sales_count, stock, created_by_role,
                    (price * sales_count) AS revenue
             FROM varieties ORDER BY sales_count DESC'
        )->fetchAll();

        $totalSales = 0;
        $totalRevenue = 0.0;
        foreach ($rows as $r) {
            $totalSales += (int)$r['sales_count'];
            $totalRevenue += (float)$r['revenue'];
        }

        return [
            'totalVarieties' => count($rows),
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'topProducts' => array_slice($rows, 0, 8),
        ];
    }
}
