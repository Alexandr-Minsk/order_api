<?php

namespace App\Entities;

use App\Exceptions\OrderStatusException;

class Order
{
    const STATUS_NEW = 'new';
    const STATUS_PROCESSED = 'processed';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    protected $id;
    protected $status;
    protected $product_ids;
    protected $unsavedProductIds = [];

    /**
     * Order constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if(!empty($data))
            $this->hydrate($data);
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data)
    {
        foreach ($data as $attribute => $value) {
            $method = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $attribute)));
            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param string $status
     * @throws OrderStatusException
     */
    public function setStatus($status) {
        switch ($this->status) {
            case null:
                $canSetStatus = true;
                break;
            case Order::STATUS_NEW:
                $canSetStatus = in_array($status, [
                    Order::STATUS_NEW,
                    Order::STATUS_PROCESSED,
                    Order::STATUS_CANCELED
                ]);
                break;
            case Order::STATUS_PROCESSED:
                $canSetStatus = in_array($status, [
                    Order::STATUS_PROCESSED,
                    Order::STATUS_TRANSFERRED,
                    Order::STATUS_CANCELED
                ]);
                break;
            case Order::STATUS_TRANSFERRED:
                $canSetStatus = in_array($status, [
                    Order::STATUS_TRANSFERRED,
                    Order::STATUS_COMPLETED,
                    Order::STATUS_CANCELED
                ]);
                break;
            default: $canSetStatus = false;
        }
        if ($canSetStatus) {
            $this->status = $status;
        } else {
            throw new OrderStatusException('Invalid order status');
        }
    }

    /**
     * @return array
     */
    public function getProductIds() {
        return $this->product_ids;
    }

    /**
     * @param $productIds
     */
    public function setProductIds($productIds) {
        $this->product_ids = $productIds;
    }

    /**
     * @param array $product_ids
     * @throws OrderStatusException
     */
    public function addProductIds(array $product_ids) {
        if ($this->status !== self::STATUS_NEW) {
            throw new OrderStatusException('Invalid order status, cannot add products');
        }
        $this->unsavedProductIds = $product_ids;
    }

    /**
     * @return array
     */
    public function getUnsavedProductIds() {
        return $this->unsavedProductIds;
    }
}
