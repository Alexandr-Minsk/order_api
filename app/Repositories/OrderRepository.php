<?php

namespace App\Repositories;

use App\Entities\Order;
use App\Order as OrderModel;
use App\OrderItem;
use App\Product;

class OrderRepository implements OrderRepositoryInterface

{
    /**
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function get($id) {
        /** @var \App\Order $orderModel */
        $orderModel = OrderModel::findOrFail($id);
        $productIds = $orderModel->orderItems()->get()->pluck('product_id')->toArray();
        $order = new Order();
        $order->hydrate([
            'id' => $orderModel->id,
            'status' => $orderModel->status,
            'product_ids' => $productIds,
        ]);

        return $order;
    }

    /**
     * @param Order $order
     * @return integer
     */
    public function create(Order $order) {
        $order = OrderModel::create(['status' => $order->getStatus()]);

        return $order->id;
    }

    /**
     * @param Order $order
     * @throws \Exception
     */
    public function update(Order $order) {
        $orderModel = OrderModel::findOrFail($order->getId());
        $orderModel->update(['status' => $order->getStatus()]);
        $unsavedProductIds = $order->getUnsavedProductIds();
        if (!empty($unsavedProductIds)) {
            $this->addProducts($orderModel, $unsavedProductIds);
        }
    }

    /**
     * @param \App\Order $orderModel
     * @param array $productsIds
     * @return string
     * @throws \Exception
     */
    private function addProducts(OrderModel $orderModel, array $productsIds) {
        $products = Product::find($productsIds);
        if (count($products) !== count($productsIds)) {
            throw new \Exception('Have the wrong product');
        }
        foreach ($products as $product) {
            /** @var OrderItem $orderItem */
            $orderItem = new OrderItem();
            $orderItem->product_id = $product->id;
            $orderItem->order_id = $orderModel->id;
            $orderItem->save();
        }
    }
}