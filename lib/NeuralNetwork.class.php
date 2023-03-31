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

    namespace rsgrinko\ML;

    /**
     * Класс нейронной сети
     *
     * "@author Roman Grinko <rsgrinko@gmail.com
     */
    class NeuralNetwork
    {
        /** @var int $numInputs Количество нейронов первого слоя (входы) */
        private int $numInputs;

        /** @var int $numHidden Количество нейронов скрытого слоя */
        private int $numHidden;

        /** @var int $numOutputs Количество нейронов последнего слоя (выходов) */
        private int $numOutputs;

        /** @var array $hiddenWeights Веса нейронов скрытого слоя */
        private array $hiddenWeights;

        /** @var array $outputWeights Веса нейронов выходного слоя */
        private array $outputWeights;

        /** @var string|null $storageFile Файл хранилища весов */
        private ?string $storageFile = null;

        /**
         * Конструктор
         *
         * @param int         $numInputs   Количество нейронов входного слоя
         * @param int         $numHidden   Количество нейронов скрытого слоя
         * @param int         $numOutputs  Количество нейронов выходного слоя
         * @param string|null $storagePath Папка хранилища весов (путь без слеша в конце)
         */
        public function __construct(int $numInputs, int $numHidden, int $numOutputs, ?string $storagePath = null)
        {
            $this->numInputs   = $numInputs;
            $this->numHidden   = $numHidden;
            $this->numOutputs  = $numOutputs;

            if ($storagePath !== null) {
                // Задаем хранилище весов с учетом конфигурации сети
                $this->storageFile = $storagePath . '/NeuralNetworkWeights_' . $numInputs . '_' . $numHidden . '_' . $numOutputs . '.json';
            }


            // Пытаемся загрузить сохраненные значения весов
            if (!$this->loadWeights()) {
                // Если не удалось - генерируем случайно (первый запуск)

                // Инициализация случайных весов скрытого слоя
                $this->hiddenWeights = [];
                for ($i = 0; $i < $this->numInputs; $i++) {
                    for ($j = 0; $j < $this->numHidden; $j++) {
                        $this->hiddenWeights[$i][$j] = mt_rand(-100, 100) / 100;
                    }
                }

                // Инициализация случайных весов выходного слоя
                $this->outputWeights = [];
                for ($i = 0; $i < $this->numHidden; $i++) {
                    for ($j = 0; $j < $this->numOutputs; $j++) {
                        $this->outputWeights[$i][$j] = mt_rand(-100, 100) / 100;
                    }
                }
            }
        }

        /**
         * Функция активации (сигмоида)
         *
         * @param float $value Значение
         *
         * @return float Результат
         */
        private function activate(float $value): float
        {
            return 1 / (1 + exp(-$value));
        }


        /**
         * Обработка
         *
         * @param array $inputs Входные данные
         *
         * @return array Результат
         */
        public function feedForward(array $inputs): array
        {
            $hiddenOutputs = [];
            for ($i = 0; $i < $this->numHidden; $i++) {
                $sum = 0;
                for ($j = 0; $j < $this->numInputs; $j++) {
                    $sum += $inputs[$j] * $this->hiddenWeights[$j][$i];
                }
                $hiddenOutputs[$i] = $this->activate($sum);
            }

            $outputs = [];
            for ($i = 0; $i < $this->numOutputs; $i++) {
                $sum = 0;
                for ($j = 0; $j < $this->numHidden; $j++) {
                    $sum += $hiddenOutputs[$j] * $this->outputWeights[$j][$i];
                }
                $outputs[$i] = $this->activate($sum);
            }

            return $outputs;
        }

        /**
         * @param array $inputs       Входные данные
         * @param array $targets      Эталонное значение
         * @param float $learningRate Скорость обучения
         *
         * @return void
         */
        public function train(array $inputs, array $targets, float $learningRate): void
        {
            // Обработка (часть кода дублируется из предыдущего метода для получения данных слоев
            // и обработки методом обратного распространения ошибки
            $hiddenOutputs = [];
            for ($i = 0; $i < $this->numHidden; $i++) {
                $sum = 0;
                for ($j = 0; $j < $this->numInputs; $j++) {
                    $sum += $inputs[$j] * $this->hiddenWeights[$j][$i];
                }
                $hiddenOutputs[$i] = $this->activate($sum);
            }

            $outputs = [];
            for ($i = 0; $i < $this->numOutputs; $i++) {
                $sum = 0;
                for ($j = 0; $j < $this->numHidden; $j++) {
                    $sum += $hiddenOutputs[$j] * $this->outputWeights[$j][$i];
                }
                $outputs[$i] = $this->activate($sum);
            }

            // Расчет ошибок
            $outputErrors = [];
            for ($i = 0; $i < $this->numOutputs; $i++) {
                $error            = $targets[$i] - $outputs[$i];
                $outputErrors[$i] = $outputs[$i] * (1 - $outputs[$i]) * $error;
            }

            $hiddenErrors = [];
            for ($i = 0; $i < $this->numHidden; $i++) {
                $error = 0;
                for ($j = 0; $j < $this->numOutputs; $j++) {
                    $error += $outputErrors[$j] * $this->outputWeights[$i][$j];
                }
                $hiddenErrors[$i] = $hiddenOutputs[$i] * (1 - $hiddenOutputs[$i]) * $error;
            }

            // Обновление весов
            for ($i = 0; $i < $this->numHidden; $i++) {
                for ($j = 0; $j < $this->numOutputs; $j++) {
                    $delta                       = $learningRate * $outputErrors[$j] * $hiddenOutputs[$i];
                    $this->outputWeights[$i][$j] += $delta;
                }
            }

            for ($i = 0; $i < $this->numInputs; $i++) {
                for ($j = 0; $j < $this->numHidden; $j++) {
                    $delta                       = $learningRate * $hiddenErrors[$j] * $inputs[$i];
                    $this->hiddenWeights[$i][$j] += $delta;
                }
            }
        }

        /**
         * Загрузка сохраненных значений весов
         *
         * @return bool Флаг успешного выполнения
         */
        private function loadWeights(): bool
        {
            // Если нет сохраненных весов - выходим
            if (!file_exists($this->storageFile)) {
                return false;
            }

            $data = file_get_contents($this->storageFile);

            // Если данные отсутствуют - выходим
            if (empty($data)) {
                return false;
            }
            try {
                $jsonData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                return false;
            }

            // Если структура данных не совпадает с ожидаемой - выходим
            if (empty($jsonData['type'])
                || $jsonData['type'] !== 'NNW'
                || empty($jsonData['data'])) {
                return false;
            }

            $this->hiddenWeights = $jsonData['data']['hidden'];
            $this->outputWeights = $jsonData['data']['output'];

            return true;
        }

        /**
         * Сохранение значений весов
         *
         * @return bool Флаг успешного выполнения
         */
        public function saveWeights(): bool
        {
            // Если не задан путь до файла-хранилища - выходим
            if ($this->storageFile === null) {
                return false;
            }
            $data = [
                'type'      => 'NNW',
                'version'   => '1.0.0',
                'generated' => date('Y-m-d H:i:s'),
                'data'      => [
                    'hidden' => $this->hiddenWeights,
                    'output' => $this->outputWeights,
                ],
            ];
            $result = @file_put_contents($this->storageFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return !($result === false);
        }

    }
