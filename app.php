<?php

class Dashboard
{
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;
    public $clientesAtivos;
    public $clientesInativos;
    public $reclamacoes;
    public $elogios;
    public $sugestoes;
    public $despesas;

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
    public function getClientesAtivos()
    {
        $query = 'SELECT COUNT(*) as clientes_ativos FROM `tb_clientes` WHERE `cliente_ativo` = :clienteStatus ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':clienteStatus', 1);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
    }
    public function getClientesInativos()
    {
        $query = 'SELECT COUNT(*) as clientes_inativos FROM `tb_clientes` WHERE `cliente_ativo` = :clienteStatus ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':clienteStatus', 0);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }
    public function getReclamacoes()
    {
        $query = 'SELECT COUNT(*) as reclamações FROM `tb_contatos` WHERE `tipo_contato` = :tipo ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':tipo', 1);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->reclamações;
    }
    public function getElogios()
    {
        $query = 'SELECT COUNT(*) as elogios FROM `tb_contatos` WHERE `tipo_contato` = :tipo ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':tipo', 2);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->elogios;
    }
    public function getSugestoes()
    {
        $query = 'SELECT COUNT(*) as sugestoes FROM `tb_contatos` WHERE `tipo_contato` = :tipo ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':tipo', 3);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->sugestoes;
    }
    public function getDespesas()
    {
        $query = 'SELECT SUM(total) as despesas FROM `tb_despesas` WHERE `data_despesa` between :data1 and :data2 ';
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data1', $this->dashboard->data_inicio);
        $stmt->bindValue(':data2', $this->dashboard->data_fim);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->despesas;
    }
    
}

$dashboard = new Dashboard;
$conexao = new Conexao;
$bd = new Bd($conexao, $dashboard);

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

// datas para pesquisa no banco de dados
$data0 = $ano . '-' . $mes . '-' . '01';
$data1 = $ano . '-' . $mes . '-' . $dias_do_mes;

$dashboard->__set('data_inicio', $data0);
$dashboard->__set('data_fim', $data1);
$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());
$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
$dashboard->__set('clientesInativos', $bd->getClientesInativos());
$dashboard->__set('reclamacoes', $bd->getReclamacoes());
$dashboard->__set('sugestoes', $bd->getSugestoes());
$dashboard->__set('elogios', $bd->getElogios());
$dashboard->__set('despesas', $bd->getDespesas());

echo json_encode($dashboard);
