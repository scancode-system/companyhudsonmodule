@extends('dashboard::layouts.master')

@section('content')

<div class="container">
	@if (count($errors) > 0)
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	@if(session()->has('message'))
	<div class="alert alert-success">
		<i class="fa fa-check-circle fa-fw fa-lg"></i>
		<strong>Sucesso!</strong> {{ session()->get('message') }}
	</div>
	@endif
	@if(session()->has('warn'))
	<div class="alert alert-warning">
		<i class="fa fa-warning fa-fw fa-lg"></i>
		<strong>Aviso!</strong> {{ session()->get('warn') }}
	</div>
	@endif


	<div class="row">
		<div class="col-lg-12">

			<div class="main-box clearfix">
				<header class="main-box-header clearfix">
					<h1>Pedidos não importados</h1>
				</header>
				<div class="main-box-body clearfix">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Opção</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($pedidos as $pedido)
								<tr>
									<td>
										{{ $pedido->id }}
									</td>
									<td>
										{{ Form::open(['route' => ['hudson.pedidos.store', $pedido->id]]) }}
										<input type="submit" value="Importar" class="btn btn-default" />
										{{ Form::close() }}
									</td>
								</tr>
								@endforeach 
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="main-box clearfix">
				<header class="main-box-header clearfix">
					<h1>Pedidos Importados</h1>
				</header>
				<div class="main-box-body clearfix">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Orçamento</th>
									<th>Mensagem</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($hudson_pedidos_sucesso as $hudson_pedido)
								<tr>
									<td>
										{{ $hudson_pedido->order_id }}
									</td>
									<td>
										{{ $hudson_pedido->orcamento }}
									</td>
									<td>
										{{ $hudson_pedido->message }}
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="main-box clearfix">
				<header class="main-box-header clearfix">
					<h1>Validando Clientes</h1>
				</header>
				<div class="main-box-body clearfix">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Mensagem</th>
									<th class="text-right">-</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($hudson_pedidos_waiting as $hudson_pedido)
								<tr>
									<td>
										{{ $hudson_pedido->order_id }}
									</td>
									<td>
										{{ $hudson_pedido->message }}
									</td>
									<td class="text-right"> 
										{{ Form::open(['route' => ['hudson.pedidos.store', $hudson_pedido->order_id]]) }}
										<input type="submit" value="Integrar" class="btn btn-default" />
										{{ Form::close() }}
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="main-box clearfix">
				<header class="main-box-header clearfix">
					<h1>Falha na Importação</h1>
				</header>
				<div class="main-box-body clearfix">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Status</th>
									<th>Mensagem</th>
									<th class="text-right">-</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($hudson_pedidos_erro as $hudson_pedido)
								<tr>
									<td>
										{{ $hudson_pedido->order_id }}
									</td>
									<td>
										{{ $hudson_pedido->status }}
									</td>
									<td>
										{{ $hudson_pedido->message }}
									</td>
									<td>
										{{ Form::open(['route' => ['hudson.pedidos.store', $hudson_pedido->order_id]]) }}
										<input type="submit" value="Importar" class="btn btn-default" />
										{{ Form::close() }}
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection

@section('breadcrumb')
<li class="breadcrumb-item">
	<a href="{{ route('dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item">
	Produtos
</li>
@endsection