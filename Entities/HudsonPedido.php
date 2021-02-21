<?php

namespace Modules\CompanyHudson\Entities;

use Illuminate\Database\Eloquent\Model;

class HudsonPedido extends Model
{
    protected $table = 'hudson_pedidos';
    protected $fillable = ['order_id', 'status', 'message', 'orcamento'];
 
 public $timestamps = false;
    
    public function pedido()
    {
        return $this->belongsTo('App\Models\Pedido', 'id_pedido');
    }
    
    
}
