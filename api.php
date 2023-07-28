<?php
// Definir cabeçalhos HTTP para permitir solicitações de qualquer origem (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// GET /plans: Retorna todas os planos.
// GET /prices: Retorna todas os precos.
// GET /plansage/{idade}: Retorna todas categoria planos por idade.
// POST /tasks: Adiciona uma nova tarefa.

// Simulação de banco de dados usando uma matriz de tarefas
$tasks = array(
    array("id" => 1, "title" => "Tarefa 1", "completed" => false),
    array("id" => 2, "title" => "Tarefa 2", "completed" => true)
);

// Ler o conteúdo do arquivo JSON
$jsonPlans = file_get_contents('plans.json');
// Decodificar o conteúdo JSON para um array associativo
$dataPlans = json_decode($jsonPlans, true);
// Verificar se o JSON foi decodificado corretamente
if ($dataPlans === null) {
    die('Erro ao decodificar o JSON.');
}
// Ler o conteúdo do arquivo JSON
$jsonPrices = file_get_contents('prices.json');
// Decodificar o conteúdo JSON para um array associativo
$dataPrices = json_decode($jsonPrices, true);
// Verificar se o JSON foi decodificado corretamente
if ($dataPrices === null) {
    die('Erro ao decodificar o JSON.');
}

// Verificar o método HTTP da solicitação
$method = $_SERVER['REQUEST_METHOD'];

// Obter os parâmetros da URL
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('/', $uri);
$resource = $uri[3];
$id = isset($uri[4]) ? $uri[4] : null;

// Responder à solicitação adequada
switch ($method) {
    case 'GET':
        if ($resource === 'plans') {
            if($resource === 'plans' && $id !== null){
                // Retornar o plano com o ID especificado
                $plan = array_filter($dataPlans, function ($plan) use ($id) {
                    return $plan['codigo'] == $id;
                });
                echo json_encode(array_values($plan));
            }else{
                 // Retornar todas as planos
                echo json_encode($dataPlans);
            }
        } elseif ($resource === 'prices') {
            if($resource === 'prices' && $id !== null){
                // Retornar um preço com o ID especificado
                $price = array_filter($dataPrices, function ($price) use ($id) {
                    return $price['codigo'] == $id;
                });
                echo json_encode(array_values($price));
            }else{
                // Retorna o todas os precos
                echo json_encode($dataPrices);
            }  
            
        }else {
            // Rota inválida
            http_response_code(404);
            echo json_encode(array("message" => "Rota não encontrada."));
        }

        break;

    case 'POST':
        if ($resource === 'proposal') {

            // Obter o conteúdo do corpo da requisição
            $jsonContent = file_get_contents('php://input');

            // Decodificar o conteúdo JSON para um array associativo
            $data = json_decode($jsonContent, true);

            // Verificar se o JSON foi decodificado corretamente
            if ($data === null) {
                http_response_code(400); // Bad Request
                echo json_encode(array("message" => "Erro ao decodificar o JSON."));
                exit;
            }

            // Exemplo de uso dos dados do corpo da requisição
            $qntBeneficiarios = $data['qntBeneficiarios'];
            $idade = $data['idade'];
            $codigoPlano = $data['codigoPlano'];
            $nome = $data['nome'];

            // Dados a serem escritos no arquivo JSON
            $data = array(
                "qntBeneficiarios" => $qntBeneficiarios,
                "idade" => $idade,
                "codigoPlano" => $codigoPlano,
                "nome" => $nome
            );

            // Converter o array para JSON
            $jsonData = json_encode($data);

            // Escrever o JSON no arquivo
            file_put_contents('proposta.json', $jsonData);

            // Receber os dados enviados no corpo da solicitação
            // $data = json_decode(file_get_contents('php://input'), true);

            // // Criar uma nova tarefa
            // $newTask = array(
            //     "id" => count($tasks) + 1,
            //     "title" => $data['title'],
            //     "completed" => false
            // );
            // $tasks[] = $newTask;
            // echo json_encode($newTask);
        } else {
            // Rota inválida
            http_response_code(404);
            echo json_encode(array("message" => "Rota não encontrada."));
        }
        break;
    default:
        // Método HTTP não suportado
        http_response_code(405);
        echo json_encode(array("message" => "Método não suportado."));
        break;
}
