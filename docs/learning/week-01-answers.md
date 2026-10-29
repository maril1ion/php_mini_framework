### Теория и ответы на вопросы
** #A1. SAPI.** Чем PHP-FPM отличается от CLI SAPI? Почему `php -S localhost:8000` годится для проверки идеи, но не для продакшена? Что происходит с процессом PHP между двумя HTTP-запросами в FPM?
_Чем отличается CLI SAPI от FPM?_ 
- CLI SAPI выполняет работу в терминале (консольное исполнение PHP), не отвечает за WEB сервер, а FPM отвечает за обработку скриптов PHP, а также за WEB сервер. 
- _Почему `php -S localhost:8000` годится для проверки идеи, но не для продакшена?_ `php -S localhost:8000` предназначен для локального тестирования и разработки проекта, тогда как в продакшене используется собственное доменное имя, собственный выделенный сервер, который будет защищен от атак и угроз его падения. 
- _Что происходит с процессом PHP между двумя HTTP-запросами в FPM?_ 
- По сути вопросы выполняются по очереди, но вообще он выполняет их параллельно, если свободен первый запрос, то он выполняет его, даже, если он заполняет больше памяти, чем второй запрос.
#A2. **strict_types.** Что делает `declare(strict_types=1);`? Приведи два примера: один, где без него функция молча приводит тип, и один, где приведение допустимо и полезно.
- Он помогает избежать ошибок, при указании этой функции в PHP необходимо указывать явно тип: 
```
<?php
declare(strict_types=1);
function sum (int $a, int $b): int 
{
	return $a + $b;
}
sum(1,1); // 2
sum(1, "1"); // TypeError

```
При этом, будет сообщена ошибка в нарушении типа.
Без приведения типа PHP будет сам определять тип данных.
#A3. **PSR-4.** По какому алгоритму автозагрузчик найдёт класс `App\Http\Response`, если в `composer.json` указано `"App\\": "src/"`? Что произойдёт, если файл лежит в `src/Http/Response.php`, а namespace в нём — `App\Http\Responses`? Почему ошибка проявится не сразу?
_По какому алгоритму автозагрузчик найдёт класс `App\Http\Response`, если в `composer.json` указано `"App\\": "src/"`?_
По сути, он просто вместо app подставит src, найдет по дальнейшему пути и в конце файла добавить расширение .php. И приведет путь к файлу в нормальный вид (заменит \ на /).
_Что произойдёт, если файл лежит в `src/Http/Response.php`, а namespace в нём — `App\Http\Responses`? Почему ошибка проявится не сразу?_
Он будет обрабатывать и искать класс имя класса, а как мы знаем, имя класса и папка должны быть одинаковыми. Ошибка будет не сразу, потому что Composer уже заранее генерирует готовый файл ClassMap, который возможно будет лежать по неправильному пути, чтобы пересобрать Composer. 
#A4. **Точка входа.** Почему единственная публичная директория — `public/`? Что конкретно случится с `composer.json`, `.env`, `src/`, `var/logs/app.log`, если docroot указать на корень проекта? Назови минимум два риска.
Одно из опасностей - это приватные файлы, который могут стать доступными обычным пользователям, что может привести к утечке данных и атакам. 
Помимо этого есть риск доступа к исходному коду, нарушение хоста
#A5. **Интерфейс vs абстрактный класс.** Два критерия, когда выбирать интерфейс, и два — когда абстрактный класс. Приведи пример из своего CRUD-блога, где ты применил бы интерфейс (назови методы и того, кто их реализует).
Интерфейс - опреляет методы, которые должен реализовать класс.
Абстрактный класс - определяет основы для наследников классов. Он может иметь свойства и методы, конструкторы, а интерфейс - только публичные методы.
_Два критерия, когда выбирать интерфейс, и два — когда абстрактный класс:_
 Интерфейса - это наследование и точное определение метода. Его стоит имплементировать, когда необходимо объявить публичные методы, которые класс обязан реализовать, если необходимо, чтобы разные классы реализовали один и тот же интерфейс. А абстрактный класс - для определение точных свойств, определение конструктов и реализации точных методов и свойств, также, когда необходимо наследовать только один класс.
_Приведи пример из своего CRUD-блога, где ты применил бы интерфейс (назови методы и того, кто их реализует)._
К примеру вход панел администратором.
```
//LoginAdminInterface
<?php
namespace core\LoginAdmin;

interface LoginAdminInterface
{
    public function login(string $login, string $password): bool;

    public function logout(): void;

    public function getTable(): string;

    public function getName(): string;

    public function getPassword(): string;

    public function checkLogin();

    public function check(): bool;
}
```
```
//LoginAdmin - реализует интерфейс.
<?php

namespace core\LoginAdmin;

use core\Config\ConfigDBInterface;
use core\Database\DatabaseInterface;
use core\Sessions\SessionsInterface;

class LoginAdmin implements LoginAdminInterface
{
    public function __construct(private ConfigDBInterface $config, private DatabaseInterface $db, private SessionsInterface $session) {}

    public function login(string $login, string $password): bool
    {
        $admin = $this->db->first($this->getTable(), [$this->getName() => $login]);
        if (! $admin) {
            return false;
        }

        if (! password_verify($password, $admin[$this->getPassword()])) {
            return false;
        }

        $this->session->setSession('admin_id', $admin);

        return true;
    }

    public function check(): bool
    {
        return $this->session->hasSession('admin_id');
    }

    public function checkLogin()
    {
        return $this->session->getSessionFlash('loginFalse');
    }

    public function logout(): void
    {
        $this->session->removeSession('admin_id');
    }

    public function getTable(): string
    {
        return $this->config->get('admin.table');
    }

    public function getName(): string
    {
        return $this->config->get('admin.login');
    }

    public function getPassword(): string
    {
        return $this->config->get('admin.password');
    }
}

```

#A6. **Иммутабельность.** Почему метод `withHeader()` у объекта Response должен возвращать **новый** объект, а не менять `$this`? Как это связано с PSR-7 и с middleware-цепочкой?

Это необходимо для того, чтобы объекты оставались неизменяемыми. PSR-7 требует, чтобы исходный объект оставался неизменяемым.А middleware-цепочка позволяет взять копию объекта и изменить и передать дальше, не меняя исходное состояние объекта.