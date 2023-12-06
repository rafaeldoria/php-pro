<?php 

require 'bootstrap.php';

try {
    $data = router();
    extract($data['data']);

    if(!isset($data['view'])){
        throw new Exception('Index view not found');
    }

    if(!file_exists(VIEWS.$data['view'])){
        throw new Exception("This view {$data['view']} not exist");
    }

    $view = $data['view'];

    require VIEWS.'master.php';
} catch (Exception $e) {
    var_dump($e->getMessage());
}
