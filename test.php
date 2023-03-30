<?php
    require_once __DIR__ . '/bootstrap.php';
    function getStoragePath(int $id): string
    {
        return  __DIR__ . '/storage/synapseWeightStorage_' . $id . '.json';
    }

    $ob = new NeuroNetwork(getStoragePath(1));
    $res = $ob->run([1, 1]);
    echo '[Preset 1] Input data [1, 1], result: ' . $res[0] . PHP_EOL;

    echo '[Mutate] Mutate weights...' . PHP_EOL;
    $ob->mutate();

    $res = $ob->run([1, 1]);
    echo '[Preset 2] Input data [1, 1], result: ' . $res[0] . PHP_EOL;

    echo $ob->calculateTotalError(0.01);


