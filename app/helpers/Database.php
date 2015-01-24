<?php
  use \r;

class Database {
  protected $connection;
  protected $query;

  public function __construct(r\Connection $databaseConnection) {
    $this->connection = $databaseConnection;
  }

  public function __call($method, $args) {
    $this->query = call_user_func_array([$this->query, $method], $args);
    return $this;
  }

  public function table($table) {
    $this->query = \r\table($table);
    return $this;
  }

  public function tableCreate($table){
    $this->query = \r\tableCreate($table);
    return $this;
  }

  public function tableList($table){
    $this->query = \r\tableList($table);
    return $this;
  }

  public function run() {
    return $this->query->run($this->connection);
  }

}