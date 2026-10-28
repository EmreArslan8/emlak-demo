<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo) {
        return $pdo;
    }
    $fresh = !is_file(DB_PATH);
    if ($fresh && !is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0775, true);
    }
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    if ($fresh) {
        $pdo->exec(file_get_contents(APP . '/schema.sql'));
        require APP . '/seed.php';
        seed($pdo);
    }
    return $pdo;
}

function db_run(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

function db_all(string $sql, array $params = []): array
{
    return db_run($sql, $params)->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    return db_run($sql, $params)->fetch() ?: null;
}

function db_value(string $sql, array $params = []): mixed
{
    return db_run($sql, $params)->fetchColumn();
}

function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(',', $cols), implode(',', array_fill(0, count($cols), '?')));
    db_run($sql, array_values($data));
    return (int) db()->lastInsertId();
}

function db_update(string $table, int $id, array $data): void
{
    $set = implode(',', array_map(fn($c) => "$c = ?", array_keys($data)));
    db_run("UPDATE $table SET $set WHERE id = ?", [...array_values($data), $id]);
}

/** İlan satırındaki JSON alanlarını çözer. */
function hydrate_listing(?array $l): ?array
{
    if (!$l) {
        return null;
    }
    $l['images'] = json_decode($l['images'] ?: '[]', true) ?: [];
    $l['features'] = json_decode($l['features'] ?: '[]', true) ?: [];
    $l['cover'] = $l['images'][0] ?? null;
    return $l;
}

function listings_query(array $f, int $limit = 9, int $offset = 0): array
{
    $where = ["l.status != 'pasif'"];
    $p = [];
    if (!empty($f['tip']) && isset(LISTING_TYPES[$f['tip']])) {
        $where[] = 'l.type = ?';
        $p[] = $f['tip'];
    }
    if (!empty($f['kategori'])) {
        $where[] = 'l.category = ?';
        $p[] = $f['kategori'];
    }
    if (!empty($f['ilce'])) {
        $where[] = 'l.district = ?';
        $p[] = $f['ilce'];
    }
    if (!empty($f['oda'])) {
        if ($f['oda'] === '4+') {
            $where[] = "CAST(substr(l.rooms,1,1) AS INTEGER) >= 4";
        } else {
            $where[] = 'l.rooms = ?';
            $p[] = $f['oda'];
        }
    }
    foreach (['min_fiyat' => 'l.price >= ?', 'max_fiyat' => 'l.price <= ?', 'min_m2' => 'l.gross_m2 >= ?', 'max_m2' => 'l.gross_m2 <= ?'] as $k => $cond) {
        if (isset($f[$k]) && $f[$k] !== '' && is_numeric(str_replace('.', '', (string) $f[$k]))) {
            $where[] = $cond;
            $p[] = (float) str_replace('.', '', (string) $f[$k]);
        }
    }
    if (!empty($f['q'])) {
        $where[] = '(l.title LIKE ? OR l.district LIKE ? OR l.neighborhood LIKE ? OR l.description LIKE ?)';
        $like = '%' . $f['q'] . '%';
        array_push($p, $like, $like, $like, $like);
    }
    if (!empty($f['agent_id'])) {
        $where[] = 'l.agent_id = ?';
        $p[] = (int) $f['agent_id'];
    }
    $order = match ($f['sirala'] ?? '') {
        'fiyat-artan' => 'l.price ASC',
        'fiyat-azalan' => 'l.price DESC',
        'm2' => 'l.gross_m2 DESC',
        default => "(l.status = 'satildi') ASC, l.featured DESC, l.created_at DESC",
    };
    $w = implode(' AND ', $where);
    $total = (int) db_value("SELECT COUNT(*) FROM listings l WHERE $w", $p);
    $rows = db_all("SELECT l.*, a.name AS agent_name, a.photo AS agent_photo FROM listings l
        LEFT JOIN agents a ON a.id = l.agent_id WHERE $w ORDER BY $order LIMIT $limit OFFSET $offset", $p);
    return [array_map('hydrate_listing', $rows), $total];
}
