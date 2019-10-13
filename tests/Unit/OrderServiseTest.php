<?php

namespace Tests\Unit;

use App\Entities\Order;
use App\Services\OrderService;
use Tests\TestCase;
use App\Exceptions\OrderStatusException;

class OrderServiseTest extends TestCase
{
    /**
     * Create order test.
     *
     * @return void
     */
    public function testCreateOrder() {
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);
        
        $orderId = $orderService->createOrder();
        $this->assertTrue($orderId === 1);
        $order = $orderService->get($orderId);
        $this->assertEquals(Order::STATUS_NEW, $order->getStatus());
        
        $orderId = $orderService->createOrder();
        $this->assertTrue($orderId === 2);
    }

    /**
     * @test
     * @dataProvider successChangeOrderStatusProvider
     */
    public function testSuccessChangeOrderStatus($status, $newStatus) {
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);
        
        $order = $this->getOrderWithStatus($status);
        
        $orderService->updateOrder($order->getId(), ['status' => $newStatus]);
        $order = $orderService->get($order->getId());
        $this->assertTrue($order->getStatus() === $newStatus, "Order must have a status = $newStatus");
    }

    /**
     * @return array
     */
    public function successChangeOrderStatusProvider() {
        return [
            [Order::STATUS_NEW, Order::STATUS_CANCELED],
            [Order::STATUS_NEW, Order::STATUS_NEW],
            [Order::STATUS_NEW, Order::STATUS_PROCESSED],
            [Order::STATUS_PROCESSED, Order::STATUS_CANCELED],
            [Order::STATUS_PROCESSED, Order::STATUS_PROCESSED],
            [Order::STATUS_PROCESSED, Order::STATUS_TRANSFERRED],
            [Order::STATUS_TRANSFERRED, Order::STATUS_CANCELED],
            [Order::STATUS_TRANSFERRED, Order::STATUS_TRANSFERRED],
            [Order::STATUS_TRANSFERRED, Order::STATUS_COMPLETED],
        ];
    }

    /**
     * @test
     * @dataProvider failedChangeOrderStatusProvider
     */
    public function testFailedChangeOrderStatus($status, $newStatus) {
        $this->expectException(OrderStatusException::class);
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);

        $order = $this->getOrderWithStatus($status);

        $orderService->updateOrder($order->getId(), ['status' => $newStatus]);
    }

    /**
     * @return array
     */
    public function failedChangeOrderStatusProvider() {
        return [
            [Order::STATUS_NEW, Order::STATUS_TRANSFERRED],
            [Order::STATUS_NEW, Order::STATUS_COMPLETED],
            [Order::STATUS_PROCESSED, Order::STATUS_NEW],
            [Order::STATUS_PROCESSED, Order::STATUS_COMPLETED],
            [Order::STATUS_TRANSFERRED, Order::STATUS_NEW],
            [Order::STATUS_TRANSFERRED, Order::STATUS_PROCESSED],
        ];
    }

    /**
     * Add product to order test.
     *
     * @return void
     */
    public function testSuccessAddProducts() {
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);

        $order = $this->getOrderWithStatus(Order::STATUS_NEW);
        $productIds = [1, 2];
        $orderService->updateOrder($order->getId(), ['product_ids' => $productIds]);
        $order = $orderService->get($order->getId());
        $this->assertTrue($order->getProductIds() === $productIds);
        $orderService->updateOrder($order->getId(), ['product_ids' => $productIds]);
        $order = $orderService->get($order->getId());
        $this->assertTrue($order->getProductIds() === [1, 2, 1, 2]);
    }
    
    /**
     * @test
     * @dataProvider failedAddProductsProvider
     */
    public function testFailedAddProducts($status) {
        $this->expectException(OrderStatusException::class);
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);

        $order = $this->getOrderWithStatus($status);
        $productIds = [1, 2];
        $orderService->updateOrder($order->getId(), ['product_ids' => $productIds]);
    }

    /**
     * @return array
     */
    public function failedAddProductsProvider() {
        return [
            [Order::STATUS_CANCELED],
            [Order::STATUS_PROCESSED],
            [Order::STATUS_TRANSFERRED],
            [Order::STATUS_COMPLETED],
        ];
    }

    /**
     * @param $status
     * @return Order
     * @throws \Exception
     */
    private function getOrderWithStatus($status) {
        /** @var OrderService $orderService */
        $orderService = app()->make(OrderService::class);
        $orderId = $orderService->createOrder();

        if ($status === Order::STATUS_CANCELED) {
            $orderService->updateOrder($orderId, ['status' => Order::STATUS_CANCELED]);
        }
        if (in_array($status, [Order::STATUS_PROCESSED, Order::STATUS_TRANSFERRED, Order::STATUS_COMPLETED])) {
            $orderService->updateOrder($orderId, ['status' => Order::STATUS_PROCESSED]);
        }
        if (in_array($status, [Order::STATUS_TRANSFERRED, Order::STATUS_COMPLETED])) {
            $orderService->updateOrder($orderId, ['status' => Order::STATUS_TRANSFERRED]);
        }
        if ($status === Order::STATUS_COMPLETED) {
            $orderService->updateOrder($orderId, ['status' => Order::STATUS_COMPLETED]);
        }

        return $orderService->get($orderId);
    }
}
