<?php
require_once "../function/Singleton.php";
require_once "../function/BaseModel.php";

class Test extends BaseModel
{
    use Singleton;

    protected $table = "log";

    private function __construct()
    {
        parent::__construct($this->table);
    }

    public function select()
    {
        $this->database->select(
            $this->table,
            ["id", "content"],
            function ($result) {
                var_dump($result);
            }
        );
    }
}

Test::getInstance()->select();
new Test();
