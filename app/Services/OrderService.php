<?php

namespace App\Services;

use App\Entities\Order;
use App\Repositories\OrderRepositoryInterface;
use App\Validators\OrderValidator;

/**
 * Class OrderService
 * @package App\Services
 */
class OrderService
{
    private $orderRepository;

    /**
     * OrderService constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $id
     * @return Order
     */
    public function get($id) {
        return $this->orderRepository->get($id);
    }
    
    /**
     * @return integer
     */
    public function createOrder() {
        
        $order = new Order();
        $order->setStatus(Order::STATUS_NEW);
        
        return $this->orderRepository->create($order);
    }

    /**
     * @param $orderId
     * @param array $data
     * @throws \Exception
     */
    public function updateOrder($orderId, array $data) {
        $validator = OrderValidator::getOrderValidator($data);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $order = $this->orderRepository->get($orderId);

        if (isset($data['status'])) {
            $order->setStatus($data['status']);
        }
        if (isset($data['product_ids'])) {
            $order->addProductIds($data['product_ids']);
        }

        $this->orderRepository->update($order);
    }
}
