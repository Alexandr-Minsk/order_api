<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class CreateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create order';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(OrderService $orderService) {
        $orderId = $orderService->createOrder();
        $this->info("Order with id $orderId was created");
    }
}
