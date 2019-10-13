<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class UpdateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:update {orderId} {--status=} {--product_id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(OrderService $orderService) {

        list($id, $data) = $this->prepareInputData();

        if (empty($data)) {
            $this->error('No data to update');
            exit;
        }

        try{
            $orderService->updateOrder($id, $data);
            $this->info("Order was updated");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function prepareInputData() {
        $data = [];
        $id = (integer)$this->argument('orderId');
        $options = $this->options();
        if ($options['status']) {
            $data['status'] = $options['status'];
        }
        if (isset($options['product_id']) && !empty($options['product_id'])) {
            $data['product_ids'] = array_map('intval', $options['product_id']);
        }

        return [$id, $data];
    }
}
