<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ListOrderRequest;
use App\Http\Requests\Order\ShowOrderRequest;
use App\UseCase\Order\GetOrderUseCase;
use App\UseCase\Order\ListJobOrganizersUseCase;
use App\UseCase\Order\ListOrdersUseCase;
use App\UseCase\Status\ListJobStatusUseCase;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private ListOrdersUseCase $listOrdersUseCase,
        private GetOrderUseCase $getOrderUseCase,
        private ListJobOrganizersUseCase $listJobOrganizersUseCase,
        private ListJobStatusUseCase $listJobStatusUseCase,
    ) {}

    public function index(ListOrderRequest $request): Response
    {
        $output = $this->listOrdersUseCase->execute($request->validated());

        return Inertia::render('Order/Index', [
            'orders' => $output,
            'orderStatuses' => $this->orderStatusOptions(),
        ]);
    }

    public function edit(ShowOrderRequest $request): Response
    {
        $data = $request->validated();
        $order = $this->getOrderUseCase->execute($data['id']);
        if ($order === null) {
            abort(404);
        }

        $jobOrganizers = [];
        if ($order->jobId !== null && $order->jobId !== '') {
            $jobOrganizers = $this->listJobOrganizersUseCase->execute($order->jobId);
        }

        return Inertia::render('Order/EditOs', [
            'order' => $order,
            'jobOrganizers' => $jobOrganizers,
        ]);
    }

    /**
     * @return array<int, array{id: string, name: string}>
     */
    private function orderStatusOptions(): array
    {
        $listed = $this->listJobStatusUseCase->execute([
            'type' => 'os',
            'sortField' => 'status',
            'sortDirection' => 'asc',
        ]);

        $options = [];
        foreach ($listed['data'] as $status) {
            $options[] = [
                'id' => $status->id,
                'name' => $status->status,
            ];
        }

        return $options;
    }
}
