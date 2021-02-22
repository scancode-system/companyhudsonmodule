<?php

//namespace App\Package\Hudson\controllers;
namespace Modules\CompanyHudson\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\CompanyHudson\Entities\HudsonPedido;
//use App\Package\Hudson\models\Pedido;
use Modules\CompanyHudson\Entities\Order;
//use App\Package\Hudson\services\HudsonApi;
use Modules\CompanyHudson\Services\HudsonApi;
use Illuminate\Http\Request;
use Excption;
use Illuminate\Routing\Controller;

class HudsonController extends Controller {

	public function index() {

		$hudson_pedidos = HudsonPedido::all();

		$pedidos = Order::where('status_id', 2)->doesntHave('hudson_pedido')->get();
		/*foreach ($pedidos as $i => $pedido) {
			$total = 0;
			foreach ($pedido->items as $pedido_item) {
				$total += ($pedido_item->preco * $pedido_item->quantidade) * (1 + ($pedido_item->produto->ipi / 100) );
			}
			$pedidos[$i]['total'] = $total;

			$pedidos[$i]['total'] = $total * (1 - ($pedido->pedido_pagamento->desconto/100) );
		}*/

		$hudson_pedidos_sucesso = HudsonPedido::where('status', 201)->get();
		$hudson_pedidos_waiting = HudsonPedido::where('status', 0)->get();
		$hudson_pedidos_erro = HudsonPedido::where('status', '<>', 201)->where('status', '<>', 0)->get();


		return view('companyhudson::api.index', ['pedidos' => $pedidos,'hudson_pedidos_sucesso' => $hudson_pedidos_sucesso,'hudson_pedidos_erro' => $hudson_pedidos_erro, 'hudson_pedidos_waiting' => $hudson_pedidos_waiting]);
	}


	public function store(Request $request, Order $pedido){
		$hudson_api = new HudsonApi();
		$data = $hudson_api->import($pedido);

		try {
			$hudson_pedido = HudsonPedido::where('order_id', $pedido->id)->first();
			if(!$hudson_pedido)
				HudsonPedido::create(['order_id' => $pedido->id, 'status' => $data['status'], 'message' => $data['message'], 'orcamento' => $data['orcamento']]);
			else {
				$hudson_pedido->update(['status' => $data['status'], 'message' => $data['message'], 'orcamento' => $data['orcamento']]);
			}
		} catch(Excption $e){
			
		}

		if($data['status'] == 201){
			return back()->with('message', 'Pedido Integrado com sucesso.');
		} elseif($data['status'] == 0) {
			return back()->with('warn', 'Pedido não pode ser cadastrado, pois o cliente é novo, aguardando a validação do mesmo.');
		} else {
			return back()->withErrors('Falha na integração: '.$data['message']);
		}
	}

	public function store_retry(Request $request, Order $pedido){
		$hudson_api = new HudsonApi();
		$data = $hudson_api->import($pedido);

		$hudson_pedido = HudsonPedido::where('id_pedido', $pedido->id)->first();
		$hudson_pedido->update(['status' => $data['status'], 'message' => $data['message'], 'orcamento' => $data['orcamento']]);

		if($data['status'] == 201){
			return back()->with('message', 'Pedido Integrado com sucesso.');
		} elseif($data['status'] == 0) {
			return back()->with('warn', 'Pedido não pode ser cadastrado, pois o cliente é novo, aguardando a validação do mesmo.');
		} else {
			return back()->withErrors('Falha na integração: '.$data['message']);
		}
	}

}
