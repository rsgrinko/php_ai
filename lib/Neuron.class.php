<?php
    /**
     * Класс нейрона
     */
    class Neuron
    {

        /** @var int Идентификатор нейрона */
        private int $id;

        /** @var float|null Текущее значение */
        private ?float $value;

        /**
         * Конструктор
         *
         * @param int $id Идентификатор нейрона
         */
        public function __construct(int $id)
        {
            $this->id = $id;
        }

        /** Получить текущее значение */
        public function getValue(): ?float
        {
            return $this->value;
        }

        /** Задать текущее значение */
        public function setValue(float $value): self
        {
            $this->value = $value;
            return $this;
        }

        /**
         * Метод активации
         *
         * @param float $value Значение
         * @param float $weight Коэффициент синапса
         */
        public function activate(float $value, float $weight): self
        {

            // Умножение значения на коэфициент
            $this->value = $value * $weight;


            // Гиперболический тангенс
            // https://otus.ru/journal/kak-sozdat-nejroset/
            // Функция активации
            //$this->value = tanh($value * $weight);
            return $this;
        }
    }
