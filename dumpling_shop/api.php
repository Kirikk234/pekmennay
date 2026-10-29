<?php
require_once __DIR__ . '/inc/functions.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addToCart((int)($_POST['id'] ?? 0));
            break;
        case 'update':
            updateCart((int)($_POST['id'] ?? 0), (int)($_POST['delta'] ?? 0));
            break;
        case 'remove':
            removeFromCart((int)($_POST['id'] ?? 0));
            break;
        case 'get':
            echo json_encode(getCartData());
            exit;
        case 'login':
            doLogin($_POST['email'] ?? '', $_POST['password'] ?? '');
            break;
        case 'register':
            doRegister($_POST['name'] ?? '', $_POST['email'] ?? '',
                       $_POST['password'] ?? '', $_POST['phone'] ?? '');
            break;
        case 'checkout':
            doCheckout($_POST['address'] ?? '');
            break;
        default:
            throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

// ---------- Корзина ----------
function addToCart(int $id): void {
    if (!$id) throw new Exception('bad id');
    if (isLoggedIn()) {
        $pdo = getPDO();
        $st = $pdo->prepare("SELECT id FROM cart WHERE user_id=? AND product_id=?");
        $st->execute([$_SESSION['user_id'], $id]);
        if ($row = $st->fetch()) {
            $pdo->prepare("UPDATE cart SET quantity=quantity+1 WHERE id=?")->execute([$row['id']]);
        } else {
            $pdo->prepare("INSERT INTO cart(user_id,product_id,quantity) VALUES(?,?,1)")
                ->execute([$_SESSION['user_id'], $id]);
        }
    } else {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    echo json_encode(['ok' => true, 'count' => cartCount()]);
}

function updateCart(int $id, int $delta): void {
    if (isLoggedIn()) {
        $st = getPDO()->prepare("UPDATE cart SET quantity = GREATEST(1, quantity + ?) WHERE user_id=? AND product_id=?");
        $st->execute([$delta, $_SESSION['user_id'], $id]);
    } elseif (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = max(1, $_SESSION['cart'][$id] + $delta);
    }
    echo json_encode(['ok' => true, 'count' => cartCount()]);
}

function removeFromCart(int $id): void {
    if (isLoggedIn()) {
        getPDO()->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?")
                ->execute([$_SESSION['user_id'], $id]);
    } else {
        unset($_SESSION['cart'][$id]);
    }
    echo json_encode(['ok' => true, 'count' => cartCount()]);
}

function getCartData(): array {
    $items = []; $total = 0;
    if (isLoggedIn()) {
        $st = getPDO()->prepare(
          "SELECT c.quantity, p.id, p.name, p.price
           FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id=?"
        );
        $st->execute([$_SESSION['user_id']]);
        $items = $st->fetchAll();
    } elseif (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $in  = implode(',', array_fill(0, count($ids), '?'));
        $st  = getPDO()->prepare("SELECT id,name,price FROM products WHERE id IN ($in)");
        $st->execute($ids);
        foreach ($st->fetchAll() as $p) {
            $p['quantity'] = $_SESSION['cart'][$p['id']];
            $items[] = $p;
        }
    }
    foreach ($items as &$i) { $i['sum'] = $i['price'] * $i['quantity']; $total += $i['sum']; }
    return ['ok' => true, 'items' => $items, 'total' => $total];
}

// ---------- Авторизация ----------
function doLogin(string $email, string $pass): void {
    $st = getPDO()->prepare("SELECT id,password_hash FROM users WHERE email=?");
    $st->execute([$email]);
    $u = $st->fetch();
    if (!$u || !password_verify($pass, $u['password_hash']))
        throw new Exception('Неверный email или пароль');
    $_SESSION['user_id'] = $u['id'];
    echo json_encode(['ok' => true]);
}

function doRegister(string $name, string $email, string $pass, string $phone): void {
    if (!$name || !$email || strlen($pass) < 6)
        throw new Exception('Заполните все поля (пароль ≥ 6 символов)');
    $pdo = getPDO();
    $st = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $st->execute([$email]);
    if ($st->fetch()) throw new Exception('Email уже занят');
    $pdo->prepare("INSERT INTO users(name,email,password_hash,phone) VALUES(?,?,?,?)")
        ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $phone]);
    $_SESSION['user_id'] = $pdo->lastInsertId();
    echo json_encode(['ok' => true]);
}

// ---------- Оформление заказа ----------
function doCheckout(string $address): void {
    if (!isLoggedIn()) throw new Exception('Нужно войти');
    $data = getCartData();
    if (!$data['items']) throw new Exception('Корзина пуста');

    $pdo = getPDO();
    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO orders(user_id,total,address) VALUES(?,?,?)")
            ->execute([$_SESSION['user_id'], $data['total'], $address]);
        $orderId = $pdo->lastInsertId();
        $ins = $pdo->prepare("INSERT INTO order_items(order_id,product_id,quantity,price) VALUES(?,?,?,?)");
        foreach ($data['items'] as $i) {
            $ins->execute([$orderId, $i['id'], $i['quantity'], $i['price']]);
        }
        $pdo->prepare("DELETE FROM cart WHERE user_id=?")->execute([$_SESSION['user_id']]);
        $pdo->commit();
        echo json_encode(['ok' => true, 'order_id' => $orderId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}