# Выполнение тестового задания для Junior Web Developer

Написана php-функция `config($optionName, $defaultValue)` для получения неких настроек проекта.

Настройки хранятся в php-файле `settings.php`.

Вызовы функции:
```php
echo config("site_url"); // http://mysite.ru
echo config("db.user"); // admin
echo config("app.services.resizer.fallback_format"); // jpeg
```

Есть возможность указать значение по-умолчанию, которое вернется, если опции в файле нет:
```php
echo config("db.host", "localhost"); // localhost
```

Если опции нет, и значение по-умолчанию не задано, бросается исключение с текстом: "В настройках нет такой опции!".
