<?php
    require_once __DIR__ . '/bootstrap.php';
    function getStoragePath(int $id): string
    {
        return  __DIR__ . '/storage/synapseWeightStorage_' . $id . '.json';
    }

    $ob = new NeuroNetwork(getStoragePath(1));
    $res = $ob->run([1, 1]);
    print_r($res);

    $ob->mutate();
    $res = $ob->run([1, 1]);
    print_r($res);



