<?php
declare(strict_types=1);

namespace App\Http\Route;

final class RouteCompiler
{
    public function compiler(string $pattern): string
    {
        // 1. Разбиваем строку, сохраняя плейсхолдеры целиком: {id} или {id:\d+}
        // #(\{.+?\})# — эта регулярка найдет всё внутри скобок и сохранит сами скобки
        $segments = preg_split('#(\{.+?\})#', $pattern, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $regex = '';

        foreach ($segments as $segment) {
            // Если сегмент начинается с { и заканчивается на } — это наш плейсхолдер
            if (str_starts_with($segment, '{') && str_ends_with($segment, '}')) {
                // Убираем внешние скобки: {id:\d+} -> id:\d+
                $inner = trim($segment, '{}');

                // Разделяем имя и возможную регулярку (по первому двоеточию)
                $parts = explode(':', $inner, 2);
                $name = $parts[0];
                $constraint = $parts[1] ?? '[^/]+'; // Если регулярки нет, берем "всё до следующего слеша"

                $regex .= "(?P<$name>$constraint)";
            } else {
                // Если это обычный текст (статика), просто экранируем спецсимволы
                $regex .= preg_quote($segment, '#');
            }
        }

        return '#^' . $regex . '$#';
    }
}