<?php

    /**
     * Copyright (c) 2022 Roman Grinko <rsgrinko@gmail.com>
     * Permission is hereby granted, free of charge, to any person obtaining
     * a copy of this software and associated documentation files (the
     * "Software"), to deal in the Software without restriction, including
     * without limitation the rights to use, copy, modify, merge, publish,
     * distribute, sublicense, and/or sell copies of the Software, and to
     * permit persons to whom the Software is furnished to do so, subject to
     * the following conditions:
     * The above copyright notice and this permission notice shall be included
     * in all copies or substantial portions of the Software.
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
     * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
     * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
     * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
     * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
     * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
     * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     */

    namespace rsgrinko;

    /**
     * Класс системных функций
     *
     * @author Roman Grinko <rsgrinko@gmail.com
     */
    class Console
    {
        /** @var string CLI_COLOR_RED Красный */
        public const CLI_COLOR_RED = "\033[1;31m";

        /** @var string CLI_COLOR_GREEN Зеленый */
        public const CLI_COLOR_GREEN = "\033[1;32m";

        /** @var string CLI_COLOR_BLUE Синий */
        public const CLI_COLOR_BLUE = "\033[1;34m";

        /** @var string CLI_COLOR_DEFAULT Дефолтный */
        public const CLI_COLOR_DEFAULT = "\033[0m";

        /** @var string[] Разрешенные цвета */
        public const ALLOWED_CLI_COLOR_LIST = [
            self::CLI_COLOR_RED,
            self::CLI_COLOR_GREEN,
            self::CLI_COLOR_BLUE,
            self::CLI_COLOR_DEFAULT,
        ];

        /**
         * Вывод данных в консоль
         *
         * @param string $text  Текст
         * @param string $color Цвет
         *
         * @return void
         */
        public static function log(string $text, string $color = self::CLI_COLOR_DEFAULT): void
        {
            if (!in_array($color, self::ALLOWED_CLI_COLOR_LIST, true)) {
                echo $text;
                return;
            }
            echo $color . $text . self::CLI_COLOR_DEFAULT . PHP_EOL;
        }
    }