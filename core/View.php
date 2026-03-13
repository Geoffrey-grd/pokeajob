<?php

namespace Core;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View {

    private static $twig;

    public static function init() {

    if (!self::$twig) {

        $loader = new FilesystemLoader(__DIR__ . '/../app/views');

        self::$twig = new Environment($loader, [
            'cache' => false
        ]);

        // Ajout du filtre nl2br
        $filter = new \Twig\TwigFilter('nl2br', function (string $string): string {
            return nl2br(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'), false);
        }, ['is_safe' => ['html']]);

        self::$twig->addFilter($filter);
    }

    return self::$twig;
}

    public static function render($template, $data = []) {

        $twig = self::init();

        echo $twig->render($template, $data);

    }

}
