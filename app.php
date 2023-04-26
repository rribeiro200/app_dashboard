<?php

    //classe dashboard
    class Dashboard{
        public $data_inicio;
        public $data_fim;
        public $numeroVendas;
        public $totalVendas;
        public $clientesAtivos;
        public $clientesInativos;
        public $totalReclamacoes;
        public $totalElogios;
        public $totalSugestoes;
        public $totalDespesas;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
            return $this; //retorno do próprio objeto (Dashboard) após o setter de valor ao atributo
        }
    }

    //classe de conexão com o banco de dados
    class Conexao{
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar(){
            try{
                $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname", 
                "$this->user", 
                "$this->pass");

                $conexao->exec('set charset utf8');

                return $conexao; //try... retornar conexão após as configurações

            } catch(PDOException $e){
                echo '<p>'. $e->getMessage(). '</p>'; 
            }
        }
    }

    //classe (model)
    class Bd{
        private $conexao;
        private $dashboard;

        /*A lógica por trás dessa linha de código é basicamente:
        Atribuir ao atributo privado $conexao da classe atual o retorno do método conectar() do objeto $conexao, 
        que é uma instância da classe PDO utilizada para realizar a conexão com o banco de dados.
        Atribuir ao atributo privado $dashboard da classe atual o objeto $dashboard recebido como parâmetro.
        Resumindo, essa linha de código está inicializando dois atributos privados da classe com objetos que, 
        serão utilizados em outros métodos da mesma.*/
        public function __construct(Conexao $conexao, Dashboard $dashboard){ //tipando variaveis a objetos
            
            /*atribuindo ao atributo privado 'conexao' de Bd o retorno do método 'conectar' 
            que é a instancia da conexao utilizando a Classe PDO*/
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        //método responsável por recuperar o indicador de numero de vendas do banco de dados, tb_vendas
        public function getNumeroVendas(){
            $query = 'SELECT 
                        count(*) as numero_vendas 
                    from 
                        tb_vendas 
                    where 
                        data_venda BETWEEN :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query); //prepare retorna o objeto PDOStatement
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));//valores serão recebidos na query
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));//valores serão recebidos na query
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas; //retornar o valor recuperado do banco de dados em função dessa Query
        }

        public function getTotalVendas(){
            $query = 'SELECT 
                        SUM(total) as total_vendas
                    from 
                        tb_vendas 
                    where 
                        data_venda BETWEEN :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query); //prepare retorna o objeto PDOStatement
            
            /*Este script está vinculado a uma consulta SQL que usa valores de parâmetros dinâmicos e é executado em um banco de dados.
            A linha em questão está associando um valor de uma propriedade do objeto $this->dashboard com o parâmetro :data_inicio, 
            que será usado na consulta.
            A função bindValue() vincula o valor da propriedade ao parâmetro da consulta e 
            garante que o valor da propriedade seja tratado como um valor seguro, evitando assim possíveis ataques de injeção de SQL.
            Assumindo que $this->dashboard->__get('data_inicio') retorna um valor de data válida, 
            a lógica é que a consulta SQL usará essa data como valor para o parâmetro :data_inicio na cláusula WHERE da consulta. */
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));//valores serão recebidos na query
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));//valores serão recebidos na query
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas; //retornar o valor recuperado do banco de dados em função dessa Query
        }

        public function getClientesAtivos(){
            $query = 'SELECT COUNT(*) as clientes_ativos FROM tb_clientes WHERE cliente_ativo = 1';

            $stmt = $this->conexao->prepare($query); //preparando a query
            $stmt->execute(); //executando a query

            /* retorno da função
            o número de clientes ativos na consulta SQL por meio de um objeto 
            retornado pela função fetch() do objeto $stmt. */
            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
        }

        public function getClientesInativos(){
            $query = 'SELECT COUNT(*) as clientes_inativos FROM tb_clientes WHERE cliente_ativo = 0';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
        }

        public function getTotalReclamações(){
            $query = 'SELECT SUM(tipo_contato = 1) as total_reclamacoes FROM tb_contatos';

            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
        }

        public function getTotalElogios() {
 
            $query = 'SELECT SUM(tipo_contato = 2) as total_elogios FROM tb_contatos';
       
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
       
            return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
       
          }

          public function getTotalSugestoes() {
 
            $query = 'SELECT SUM(tipo_contato = 3) as total_sugestoes FROM tb_contatos';
       
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
       
            return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
       
          }

          public function getTotalDespesas(){
            $query = 'SELECT SUM(total) as total_despesas FROM tb_despesas WHERE data_despesa BETWEEN :data_inicio AND :data_fim';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
          }

    };

    //lógica do script
        $dashboard = new Dashboard();
        $conexao = new Conexao();

        $competencia = explode('-', $_GET['competencia']);
        $ano = $competencia[0]; //ano
        $mes = $competencia[1]; //mês

        /* Como resultado da chamada dessa função, nós vamos descobrir quantos dias naquele respectivo
        mes, daquele respectivo ano  */
        $dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

                                       //datas dinamicas, formadas através da superglobal $_GET 
        $dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
        $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

        /*passando as instancias dos objetos como parametro para ser recebidos 
        no parametro do construct na classe Bd*/
        $bd = new Bd($conexao, $dashboard);

        //setando atributos com o valor das querys retornados pelos métodos do objeto Bd
        $dashboard->__set('numeroVendas', $bd->getNumeroVendas());
        $dashboard->__set('totalVendas', $bd->getTotalVendas());
        $dashboard->__set('clientesAtivos', $bd->getClientesAtivos());
        $dashboard->__set('clientesInativos', $bd->getClientesInativos());
        $dashboard->__set('totalReclamacoes', $bd->getTotalReclamações());
        $dashboard->__set('totalElogios', $bd->getTotalElogios());
        $dashboard->__set('totalSugestoes', $bd->getTotalSugestoes());
        $dashboard->__set('totalDespesas', $bd->getTotalDespesas());

        
        /* Encaminhando para a função json_encode o nosso objeto Dashboard(configurado na lógica do script back-end)
           e a função json_encode nativa do PHP vai transcrever esse objeto para uma string JSON
           na sequencia, essa string JSON retornada pela função vai ser impressa no documento 
           e essa resposta vai ser encaminhada/anexa ao body do request  */
        echo json_encode($dashboard);
?>