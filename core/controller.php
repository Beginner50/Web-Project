<!-- The base class for all controllers -->
<?php
class Controller
{
    // Load a model
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Load a view
    public function view($view, $data = [])
    {
        require_once '../app/views' . $view . '.php';
    }
}
