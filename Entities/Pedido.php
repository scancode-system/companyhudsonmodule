<?php

namespace App\Package\Hudson\models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model {

    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    protected $fillable = ['id_cliente', 'id_pedido', 'id_representante', 'observacao', 'id_status', 'data_abertura', 'entrega', 'assinatura_cliente', 'nome_comprador', 'email_comprador', 'tel_comprador', 'change', 'data_fechamento', 'transportadora'];
    public $timestamps = false;
    
    protected $dates = [
        'data_abertura'
    ];

    public function pedido_itens() {
        return $this->hasMany('App\Models\PedidoItem', 'id_pedido');
    }

    public function pedido_pagamento() {
        return $this->hasOne('App\Models\PedidoPagamento', 'id_pedido');
    }
    
    public function pedido_nfe() {
        return $this->hasOne('App\Models\PedidoNfe', 'id_pedido');
    }

    public function cliente() {
        return $this->belongsTo('App\Models\Cliente', 'id_cliente');
    }

    public function representante() {
        return $this->belongsTo('App\Models\Representante', 'id_representante');
    }

    public function pedido_status() {
        return $this->belongsTo('App\Models\PedidoStatus', 'id_status');
    }



    public function hudson_pedido()
    {
        return $this->hasOne('App\Package\Hudson\models\HudsonPedido', 'id_pedido');
    }

    public function getSql() {
        $builder = $this->getBuilder();
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }
    
    // Custom Functions
    public static function destroyNoItems(){
        $ids = Pedido::where('id_status', '<>', 2)->
                where('id_status', '<>', 6)->
                doesntHave('pedido_itens')->get()->pluck('id_pedido')->toArray();
        
        Pedido::destroy($ids);
        return $ids;
    }

}
