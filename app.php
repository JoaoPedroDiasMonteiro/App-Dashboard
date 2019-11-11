<?php

class Dashboard
{
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;

    public function __get($attr)
    {
        return $this->$attr;
    }
    public function __set($name, $value)
    {
        $this->$name = $value;
        return $this;
    }
}

class Conexao
{
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $password = '';

    public function conectar()
    {
        try {
            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;",
                "$this->user",
                "$this->password"
            );

            $conexao->exec('set charset set utf8');

            return $conexao;
        } catch (PDOException $e) {
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }
}

class Bd
{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard)
    {
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }
    public function getNumeroVendas()
    {
        $query = '
        select count(*) as numero_vendas
        from tb_vendas
        where data_venda between :datai and :dataf

        ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':datai', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':dataf', $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }
    public function getTotalVendas()
    {
        $query = '
        select SUM(total) as total_vendas
        from tb_vendas
        where data_venda between :datai and :dataf

        ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':datai', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':dataf', $this->dashboard->__get('data_fim'));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }
}

$dashboard = new Dashboard;
$conexao = new Conexao;
$bd = new Bd($conexao, $dashboard);

$dashboard->__set('data_inicio', '2018-08-01');
$dashboard->__set('data_fim', '2018-08-31');
$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('total_vendas', $bd->getTotalVendas());

print_r($dashboard);
