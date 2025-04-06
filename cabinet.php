<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit();
}

try {
    // Подключение к БД
    $db = new PDO('mysql:host=MySQL-8.2;dbname=user;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получение данных пользователя
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Пользователь не найден.");
    }

} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
// Обработка уведомлений
$successMessage = '';
if (isset($_GET['success'])) {
    // ... ваш код ...
}
// Инициализация $post
$post = isset($_POST['post']) ? htmlspecialchars($_POST['post']) : '';
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Parma Technologies Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Montserrat", sans-serif;
            background-color: #293240;
            color: #e9ecee;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #0f2232;
            color: #e9ecee;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            border-bottom: 3px solid #ff4346;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo h1 {
            font-size: 32px;
            font-weight: 900;
            margin: 0;
            line-height: 1;
            display: flex;
            flex-direction: column;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .logo h1 .parma {
            color: #ff0000;
            font-size: 1.5em;
        }

        .logo h1 .tech {
            color: #e9ecee;
            font-size: 0.7em;
            margin-top: -5px;
        }

        .nav {
            display: flex;
            gap: 25px;
        }

        .nav-link {
            color: #e9ecee;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s, transform 0.2s;
        }

        .nav-link:hover {
            color: #ff0000;
            transform: scale(1.1);
        }

        .main-content {
            padding: 40px 0;
        }

        .employee-section {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            background-color: #0f2232;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 40px;
        }

        .employee-photo {
            flex-shrink: 0;
        }

        .photo-frame {
            border: 5px solid #ff0000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.4);
        }

        .photo-frame img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            display: block;
            filter: brightness(1.1);
        }

        .employee-info {
            flex: 1;
        }

        .employee-info h2 {
            font-size: 26px;
            color: #ff4346;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .employee-info p {
            font-size: 16px;
            margin-bottom: 10px;
            color: #e9ecee;
            font-weight: 400;
        }

        .employee-info p strong {
            color: #e9ecee;
            font-weight: 500;
        }

        .wishlist {
            flex: 1;
            background-color: #293240;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .wishlist h3 {
            font-size: 22px;
            color: #ff4346;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .wishlist ul {
            list-style: none;
        }

        .wishlist li {
            font-size: 16px;
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
            color: #e9ecee;
            font-weight: 400;
        }

        .wishlist li:before {
            content: "\f005";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #ff0000;
            position: absolute;
            left: 0;
            top: 2px;
        }

        .promotions-section {
            padding: 30px;
            background-color: #0f2232;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
        }

        .promotions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .promotions-header h2 {
            font-size: 30px;
            color: #ff4346;
            text-align: left;
            font-weight: 900;
        }

        .sort-options {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-options label {
            font-size: 16px;
            color: #e9ecee;
            font-weight: 500;
        }

        .sort-options select {
            background-color: #293240;
            color: #e9ecee;
            border: 1px solid #e9ecee;
            padding: 8px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .promotions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .promotion-card {
            background-color: #293240;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e9ecee;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 200px;
        }

        .promotion-card h3 {
            font-size: 20px;
            color: #ff4346;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .promotion-card p {
            font-size: 16px;
            color: #e9ecee;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .promotion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
        }

        .favorite-star {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #e9ecee;
            font-size: 20px;
            transition: color 0.3s;
        }

        .favorite-star.active {
            color: #ff0000;
        }

        .buy-button {
            background: transparent;
            border: 2px solid #ff0000;
            color: #e9ecee;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            margin-top: auto;
        }

        .buy-button:hover {
            background-color: #ff0000;
            color: #e9ecee;
        }

        .footer {
            background: #0f2232;
            color: #e9ecee;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
            border-top: 3px solid #ff4346;
            font-size: 14px;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .nav {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .employee-section {
                flex-direction: column;
                align-items: center;
            }

            .wishlist {
                width: 100%;
            }

            .promotions-header {
                flex-direction: column;
                gap: 15px;
            }
        }

        .avatar-button {
            background-color: transparent;
            border-radius: 2px;
        }

        .avatar-button:hover {
            background: #ff4346;
        }
    </style>
</head>

<body>
    <?php if ($successMessage): ?>
        <p style="color: green;"><?= $successMessage ?></p>
    <?php endif; ?>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>
                    <span class="parma">Parma</span><span class="tech">Technologies Group</span>
                </h1>
            </div>
            <nav class="nav">
                <a href="profile.php" class="nav-link">Сотрудник</a>
                <a href="#wishlist" class="nav-link">Список желаний</a>
                <a href="#promotions" class="nav-link">Акции</a>
                <a href="login.html" class="nav-link">Выход</a>

            </nav>
        </header>

        <main class="main-content">
            <section id="employee" class="employee-section">
                <div class="employee-photo">
                    <div class="photo-frame">
                        <?php if ($user['avatar_url']): ?>
                            <img src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Аватар" width="150">
                        <?php else: ?>
                            <img src="default-avatar.png" alt="Аватар по умолчанию" width="150">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="employee-info">
                    <h2> <?= htmlspecialchars(string: $user['username']) ?></h2>
                    <p><strong>Стаж:</strong> <?= htmlspecialchars(string: $user['experience']) ?> лет</p>
                    <p>
                        <strong>Почта:</strong> <?= htmlspecialchars(string: $user['email']) ?>
                    </p>
                    <p><strong>Должность:</strong> <?= htmlspecialchars($user['post']) ?></p>
                    <p><strong>Баланс:</strong> <span id="balance"><?= htmlspecialchars($user['balance']) ?></span>
                        баллов</p>
                    <h2>Сменить аватар:</h2>
                    <form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="avatar" accept="image" required class="avatar-input">
                        <button type="submit" class="avatar-button">Загрузить</button>
                    </form>
                </div>
                <div class="wishlist">
                    <h3>Список желаний</h3>
                    <ul id="wishlist-items"></ul>
                </div>
            </section>

            <!-- Секция акций -->
            <section id="promotions" class="promotions-section">
                <div class="promotions-header">
                    <h2>Акции</h2>
                    <div class="sort-options">
                        <label for="sort">Сортировать по:</label>
                        <select id="sort" onchange="sortPromotions()">
                            <option value="default">По умолчанию</option>
                            <option value="city">Городу</option>
                            <option value="alphabet">Алфавиту</option>
                            <option value="popularity">Популярности</option>
                        </select>
                    </div>
                </div>
                <div class="promotions-grid" id="promotions-grid">
                    <div class="promotion-card" data-city="Москва" data-popularity="3">
                        <h3>Скидка на курсы</h3>
                        <p>Получите 20% скидку на любой курс по разработке!</p>
                        <span class="favorite-star" onclick="toggleFavorite(this, 'Скидка на курсы')"><i
                                class="far fa-star"></i></span>
                        <button class="buy-button">Купить</button>
                    </div>
                    <div class="promotion-card" data-city="Санкт-Петербург" data-popularity="5">
                        <h3>Бонус за проект</h3>
                        <p>Завершайте проекты вовремя и получайте 500 баллов!</p>
                        <span class="favorite-star" onclick="toggleFavorite(this, 'Бонус за проект')"><i
                                class="far fa-star"></i></span>
                        <button class="buy-button">Купить</button>
                    </div>
                    <div class="promotion-card" data-city="Екатеринбург" data-popularity="2">
                        <h3>Технический марафон</h3>
                        <p>Участвуйте в марафоне и выигрывайте призы!</p>
                        <span class="favorite-star" onclick="toggleFavorite(this, 'Технический марафон')"><i
                                class="far fa-star"></i></span>
                        <button class="buy-button">Купить</button>
                    </div>
                </div>
            </section>
        </main>

        <!-- Футер -->
        <footer class="footer">
            <p>© 2025 Parma Technologies Group. Все права защищены.</p>
        </footer>
    </div>
    <script>
        document.addEventListener("scroll", () => {
            const sections = document.querySelectorAll("section");
            const navLinks = document.querySelectorAll(".nav-link");

            sections.forEach((section) => {
                const sectionTop = section.offsetTop - 100;
                const sectionBottom = sectionTop + section.offsetHeight;
                const scrollPosition = window.scrollY;

                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    navLinks.forEach((link) => link.classList.remove("active"));
                    document
                        .querySelector(`.nav-link[href="#${section.id}"]`)
                        .classList.add("active");
                }
            });
        });

        const style = document.createElement("style");
        style.textContent = `
    .nav-link.active {
        color: #ff0000;
        font-weight: 700;
        text-decoration: underline;
    }
`;
        document.head.appendChild(style);

        function toggleFavorite(star, promotionName) {
            const wishlist = document.getElementById("wishlist-items");
            const isActive = star.classList.toggle("active");
            const icon = star.querySelector("i");

            if (isActive) {
                icon.classList.remove("far");
                icon.classList.add("fas");
                const li = document.createElement("li");
                li.textContent = promotionName;
                li.classList.add("wishlist-item");
                wishlist.appendChild(li);
            } else {
                icon.classList.remove("fas");
                icon.classList.add("far");
                const items = wishlist.getElementsByClassName("wishlist-item");
                for (let item of items) {
                    if (item.textContent === promotionName) {
                        wishlist.removeChild(item);
                        break;
                    }
                }
            }
        }

        function sortPromotions() {
            const sortValue = document.getElementById("sort").value;
            const promotionsGrid = document.getElementById("promotions-grid");
            const promotionCards = Array.from(
                promotionsGrid.getElementsByClassName("promotion-card")
            );

            promotionCards.sort((a, b) => {
                if (sortValue === "city") {
                    return a.dataset.city.localeCompare(b.dataset.city);
                } else if (sortValue === "alphabet") {
                    return a
                        .querySelector("h3")
                        .textContent.localeCompare(b.querySelector("h3").textContent);
                } else if (sortValue === "popularity") {
                    return b.dataset.popularity - a.dataset.popularity;
                }
                return 0;
            });

            promotionsGrid.innerHTML = "";
            promotionCards.forEach((card) => {
                promotionsGrid.appendChild(card);
                const star = card.querySelector(".favorite-star");
                const promotionName = card.querySelector("h3").textContent;
                star.onclick = () => toggleFavorite(star, promotionName);
            });
        }
    </script>
</body>

</html>