<?php

namespace Modules\CompanyHudson\Services;


class HudsonApi {

    private $url;
    private $login;
    private $senha;

    public function __construct() {
        $this->url = 'http://online.hudsondobrasil.com.br:1010/restteste';
        //$this->url = 'http://online.hudsondobrasil.com.br:1010/restoficial';
        $this->login = 'webservice';
        $this->senha = 'web@hudson321';

    }


    public function import($pedido){
        $itens = [];

        
        foreach ($pedido->items as $pedido_item) {
            array_push($itens, [
                "CKPRODUTO" => $pedido_item->product->sku,
                "CKQTDVEN" => ($pedido_item->qty),
                "CKPRCVEN" => floatval($pedido_item->price),
                "CKVALOR" => floatval($pedido_item->qty*$pedido_item->price),
                "CKENTREG" => date('Ymd'),//$pedido->data_abertura->format('Ymd'),
                "CKOPER" => "21"]);
        }


        //$codigo_cliente = $pedido->cliente->codigo_hudson;//substr($pedido->id_cliente, 0, strlen($pedido->id_cliente)-2);
        //$codigo_loja = $pedido->cliente->loja_hudson;//substr($pedido->id_cliente, strlen($pedido->id_cliente)-2);
        /*
        087 - tabela atacado pra MG
086 - tabela atacado demais estados
        */
        $cjtabela = ($pedido->order_client->client->price_list_client) ?'087':'086';// ($pedido->cliente->varejo == 0)? "086":"087";  
        //$cjtabela = "086"; // ($pedido->cliente->varejo == 0)? "086":"087";  

        $data = [
            "CJCLIENTE" => "",
            "CJLOJA" => "",
            "CJCONDPAG" => "".$this->addString($pedido->order_payment->payment_id, "3", "0"),
            "CJTABELA" => $cjtabela,
            "CJVEND1" => $this->addString($pedido->order_saller->saller->login, "6", "0"),  // perguntar CJVEND1
            "CJEMISSAO" => date('Ymd'),//$pedido->data_abertura->format('Ymd'),
            "CJMENNOTA" => "",
            "CJMENPAD2" => "",
            "CJNRFEIRA" => $this->addString($pedido->id, "6", "0"),
            "CJCGC" => $pedido->order_client->cpf_cnpj,
            "CJDSGNADO" => "F",
            "ITENS" => $itens
        ];

        //dd($data);
        $data_string = json_encode($data);  
        //dd($data_string);
        $curl = curl_init($this->url.'/orcamento');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($this->login.':'.$this->senha)));    

        $response = json_decode(utf8_encode(curl_exec($curl)));
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
           // print curl_error($curl);
            //die();
        //dd(curl_error($curl));
        curl_close($curl);
            //dd($status);
            //dd($response);
        if($status == 201){
            $data = ['status' => '201', 'message' => $response->MSG, 'orcamento' => $response->ORCAMENTO];  
        } else {
//            dd($response->errorMessage);
            if($response->errorMessage == "Cliente inexistente!!!"){
               // dd('opa');
                $result_cliente = $this->cadastrarNovoCliente($pedido->order_client->client);
                if($result_cliente['status'] == 201){
                    $data = ['status' => 0, 'message' => 'Aguardando Validação do Cliente', 'orcamento' => null];
                } else {
                    $data = ['status' => -1, 'message' => 'Erro no cadastro do Cliente', 'orcamento' => null];
                }
            } elseif($response->errorMessage == "Novo Cliente ainda não efetivado!!!"){
                $data = ['status' => 0, 'message' => 'Aguardando Validação do Cliente', 'orcamento' => null]; 
            } else {
                $data = ['status' => $response->errorCode, 'message' => $response->errorMessage, 'orcamento' => null];    
            }
        }
        return $data;
    }


    protected function cadastrarNovoCliente($cliente){
        $data = [
            "Z5CNPJ" => $cliente->cpf_cnpj,
            "Z5CONTATO" => $cliente->buyer,
            "Z5DDD" => "",
            "Z5TEL" => $cliente->phone,
            "Z5EST" => $cliente->client_address->st,
            "Z5NOME" => $cliente->corporate_name,
            "Z5NREDUZ" => $cliente->fantasy_name,
            "Z5CEP" => $cliente->client_address->postcode,
            "Z5END" => $cliente->client_address->street,
            "Z5BAIRRO" => $cliente->client_address->neighborhood,
            "Z5MUN" => $cliente->client_address->city

        ];
        $data_string = json_encode($data); 

        $curl = curl_init($this->url.'/novo_cliente');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic '. base64_encode($this->login.':'.$this->senha)));    

        $response = json_decode(utf8_encode(curl_exec($curl)));
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ['response' => $response, 'status' => $status];
    }


    protected function addString($string, $lenght, $char, $before = true) {
        $tot = $lenght - mb_strlen($string);

        for ($i = 0; $i < $tot; $i++) {
            if ($before)
                $string = $char . $string;
            else
                $string = $string . $char;
        }
        return $string;
    }

}
