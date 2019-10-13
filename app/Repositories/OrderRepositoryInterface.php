<?php

namespace App\Repositories;

use App\Entities\Order;

interface OrderRepositoryInterface
{
    /**
     * @param integer $id
     * @return Order
     */
    public function get($id);

    /**+
     * @param Order $order
     * @return integer
     */
    public function create(Order $order);

    /**
     * @param Order $order
     */
    public function update(Order $order);
}