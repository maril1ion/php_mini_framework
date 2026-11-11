<?php

namespace App\Http\Route;

final class RouteCompiler
{
    private const PLACEHOLDER_REGEX = '\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/';
    public  function compiler(string $pattern): string
    {
        //1 Статика и плейсхолдеры
        $segments = preg_split(self::PLACEHOLDER_REGEX, $pattern, -1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $regex = '';

        foreach ($segments as $segment) {
            //Если это placeholder - компилируем
            if (preg_match(self::PLACEHOLDER_REGEX, $segment, $matches)) {
                $name = $matches[1];
                $constraint = $matches[2] ?? '[^/]+';
                $regex .= "(?P<$name>$constraint)";
                continue;
            }
        }
        $regex .= preg_quote($segments, '#', '#');

    return '#^' . $regex . '$#';
    }
}