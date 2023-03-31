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

    set_time_limit(0);
    require_once __DIR__ . '/bootstrap.php';

    use rsgrinko\{ML\NeuralNetwork, Console};

    // Создаем нейросеть
    $nn = new NeuralNetwork(2, 3, 1, 'storage');

    /**
     * БЛОК ОБУЧЕНИЯ
     * После обучения можно закомментировать его
     * и проверить работу с сохраненными весами
     */
    // Обучающий датасет
    $trainingSet = [
        [[0, 0], [0]],
        [[0, 1], [0]],
        [[1, 0], [0]],
        [[1, 1], [1]],
    ];

    // Количество "эпох"
    $epochs = 100000;

    // Скорость обучения
    $learningRate = 0.5;

    Console::log('=> Run training...', Console::CLI_COLOR_BLUE);
    for ($i = 0; $i < $epochs; $i++) {
        // Выбираем случайный элемент из датасета
        $trainingData = $trainingSet[array_rand($trainingSet)];
        $inputs       = $trainingData[0];
        $targets      = $trainingData[1];
        $nn->train($inputs, $targets, $learningRate);
    }
    Console::log('=> Training success', Console::CLI_COLOR_GREEN);
    // Сохраняем натренированные веса
    Console::log('=> Save Weights...', Console::CLI_COLOR_BLUE);
    $nn->saveWeights();
    Console::log('=> Weights saved', Console::CLI_COLOR_GREEN);
    /** КОНЕЦ БЛОКА ОБУЧЕНИЯ */


    /**
     * Тестирование нейросети
     */
    $testSet = [
        [0, 0],
        [1, 0],
        [0, 1],
        [1, 1],
    ];

    Console::log('=> Testing...', Console::CLI_COLOR_BLUE);

    foreach ($testSet as $testElement) {
        $result       = $nn->feedForward($testElement);
        $resultString = '=> Input: [';
        $resultString .= implode(', ', $testElement);
        $resultString .= '], result: [';
        $resultString .= implode(', ', $result);
        $resultString .= ']';
        Console::log($resultString, Console::CLI_COLOR_GREEN);
    }

