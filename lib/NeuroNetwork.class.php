<?php

   /**
    * Класс нейросети
    */
    class NeuroNetwork
    {

        /** @var string $storageFile Файл-хранилище весов */
        private $storageFile;

        /** @var array $arValues Массив-хранилище весов */
        private $arValues;

        /**
         * @var array $layers Массив массивов нейронов
         *                    [
         *                    [], // Входной слой
         *                    [], // Скрытый слой
         *                    [], // Выходной слой
         *                    ];
         */
        private array $layers;

        /** @var array Массив синапсов */
        private array $synapses;

        /**
         * Конструктор
         */
        public function __construct(?string $storageFile = null)
        {
            // Хранилище весов
            if ($storageFile === null) {
                $this->storageFile = $_SERVER['DOCUMENT_ROOT'] . '/neuroSynaps.json';
            } else {
                $this->storageFile = $storageFile;
            }

            // Слои нейросети
            // TODO по хорошему заданием структуры сети нужно заниматься вне метода и в методе формировать слои по параметрам
            $this->layers = [
                [new Neuron(1), new Neuron(2)],
                [new Neuron(3), new Neuron(4), new Neuron(5), new Neuron(6)],
                [new Neuron(7)],
            ];

            /** Строим связи нейронов */
            $this->generateSynapses();

            // Получение сохраненных данных по весам
            $this->getSynapsesValues();
        }

        /**
         * Генерируем связи между нейронами
         */
        private function generateSynapses(): void
        {
            $synapseId    = 1;
            $layersCount = count($this->layers);

            // Бежим по слоям нейронов (последний нам не нужен) и строим связи
            for ($i = 0; $i < $layersCount - 1; $i++) {
                $neuronCount = count($this->layers[$i + 1]);
                for ($j = 0; $j < $neuronCount; $j++) {
                    $neuronInCount = count($this->layers[$i]);
                    for ($k = 0; $k < $neuronInCount; $k++) {
                        $this->synapses[] = new Synapse(
                            $synapseId, $this->layers[$i][$k], $this->layers[$i + 1][$j], $this->arValues[$synapseId] ?? null
                        );
                        $synapseId++;
                    }
                }
            }
        }

        /**
         * Сохранить значения весов синапсов в файл
         *
         * @param bool $forceUpdate Принудительно перезаписать
         */
        private function saveSynapsValue(bool $forceUpdate= false): void
        {
            $data = [];
            foreach ($this->synapses as $synapse) {
                /** @var Synapse $synapse */
                $data[$synapse->getId()] = $synapse->getValue();
            }

            if ($forceUpdate || !file_exists($this->storageFile)) {
                file_put_contents($this->storageFile, json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        }

        /**
         * Получить значения весов синапсов из файла
         *
         * @return array Сохраненный раннее массив весов
         */
        private function getSynapsesValues(): array
        {
            if (!file_exists($this->storageFile)) {
                return [];
            }

            // Получаем массив весов
            $synapsValues = json_decode(file_get_contents($this->storageFile), true);

            // Устанавливаем веса для синапсов
            foreach ($this->synapses as $synapse) {
                /** @var Synapse $synapse */
                $synapse->setValue($synapsValues[$synapse->getId()]);
            }

            $this->arValues = $synapsValues;

            // Возвращаем веса
            return $synapsValues;
        }

        /**
         * Мутация веса
         *
         * @param bool $useFullRandom Использовать полностью случайный новый вес
         * @param float|null $shift Величина сдвига для мутации
         *
         * @throws Exception
         */
        public function mutate(bool $useFullRandom = false, ?float $shift = 0.1): self
        {
            foreach ($this->synapses as $synapse) {
                /** @var Synapse $synapse */
                if ($useFullRandom) {
                    // Использовать полностью случайный новый вес
                    $tempValue = mt_rand() / mt_getrandmax();
                } else {
                    // Использовать случайный вес в диапазоне "$shift < текущий < $shift"

                    // Вариант генерации 1
                    //$tempValue = random_int((int)(($synapse->getValue() - $shift) * 100), (int)(($synapse->getValue() + $shift) * 100)) / 100;

                    // Вариант генерации 2
                    $tempValue = random_int(
                                     (int)(($synapse->getValue() - $shift) * 100000000),
                                     (int)(($synapse->getValue() + $shift) * 100000000)
                                 ) / 100000000;
                }
                // Ограничиваемся диапазоном 0..1
                if ($tempValue < 0) {
                    $tempValue = 0;
                }
                if ($tempValue > 1) {
                    $tempValue = 1;
                }

                $synapse->setValue($tempValue);
            }

            $this->saveSynapsValue(true);
            return $this;
        }

        /**
         * Запуск обработки
         */
        public function run(array $arValue): array
        {
            foreach ($arValue as $key => $value) {
                $this->layers[0][$key]->setValue($value);
            }
            foreach ($this->synapses as $synapse) {
                $this->step($synapse);
            }

            $result = [];

            // Отдаем значения нейронов последнего слоя как результат
            foreach ($this->layers[2] as $neuron) {
                $result[] = $neuron->getValue();
            }
            $this->saveSynapsValue();
            return $result;
        }


        /**
         * Шаг преобразования значений между нейронами
         */
        public function step(Synapse $synapse): void
        {
            $synapse->getOutNeuron()->activate($synapse->getInNeuron()->getValue(), $synapse->getValue());
        }


        /**
         * Расчет ошибки
         *
         * @param float $target Целевое значение
         * @param float $current Текущее значение
         * @return float Величина ошибки
         */
        private function calculateError(float $target, float $current): float
        {
            return (($target - $current) ** 2) / 2;
        }

        /**
         * Расчет общей ошибки
         *
         * @param float $target Целевое значение (ПЕРЕДЕЛАТЬ)
         * @return float Величина ошибки
         */
        public function calculateTotalError(float $target = 0.0): float
        {
            $totalError = 0.0;
            $endLayer = count($this->layers) - 1;
            foreach($this->layers as $keyLayer => $layer) {
                if ($keyLayer === 0 || $keyLayer === $endLayer) {
                    continue;
                }
                foreach($layer as $neuron) {
                    /** @var Neuron $neuron */
                    // TODO требуется определять целевое значение и передавать первым аргументом ниже
                    $totalError += $this->calculateError($target, $neuron->getValue());
                }
            }

            return $totalError;
        }
    }