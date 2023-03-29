<?php
    class SystemFunctions {
        public const CLI_COLOR_RED = "\033[01;31m";
        public const CLI_COLOR_GREEN = "\033[0;32m";
        public const CLI_COLOR_BLUE = "\033[0;34m";
        public const CLI_COLOR_RED_B = "\033[1;31m";
        public const CLI_COLOR_GREEN_B = "\033[1;32m";
        public const CLI_COLOR_BLUE_B = "\033[1;34m";
        public const CLI_COLOR_DEFAULT = "\033[0m";

        public const ALLOWED_CLI_COLOR_LIST = [
            self::CLI_COLOR_RED,
            self::CLI_COLOR_GREEN,
            self::CLI_COLOR_BLUE,
            self::CLI_COLOR_RED_B,
            self::CLI_COLOR_GREEN_B,
            self::CLI_COLOR_BLUE_B,
            self::CLI_COLOR_DEFAULT,
        ];

        public static function toConsole(string $text, string $color = self::CLI_COLOR_DEFAULT): void
        {
            if (!in_array($color, self::ALLOWED_CLI_COLOR_LIST, true)) {
                echo $text;
                return;
            }
            echo $color . $text . self::CLI_COLOR_DEFAULT . PHP_EOL;
        }
    }