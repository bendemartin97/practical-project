<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Resource;
use App\Services\DatabaseService;
use App\Services\FFIService;
use App\Services\PHPService;
use App\Services\RabbitMqService;
use App\Services\RPCService;
use App\Services\SocketService;
use FFI;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MainController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $resourceCounter = Resource::query()->count();
        $bookingCounter = Booking::query()->count();

        return Inertia::render('Main', [
            'resourceCounter' => $resourceCounter,
            'bookingsCounter' => $bookingCounter,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function rpc(): JsonResponse
    {
        $rpc = new RPCService();
        $result = $rpc->calculate();
        return response()->json($result->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function ffi(): JsonResponse
    {
        $service = new FFIService();
        $result = $service->calculate();
        return response()->json($result->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function socket(): JsonResponse
    {
        $service = new SocketService();
        $result = $service->calculate();
        return response()->json($result->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function rabbitmq(): JsonResponse
    {
        $service = new RabbitMqService();
        $result = $service->calculate();
        return response()->json($result->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function php(): JsonResponse
    {
        $service = new PHPService();
        $result = $service->calculate();
        return response()->json($result->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function seedBookings(): JsonResponse
    {
        $service = new DatabaseService();
        $result = $service->seedBookings();
        return response()->json($result);
    }


    public function seedResources(): JsonResponse
    {
        $service = new DatabaseService();
        $result = $service->seedResources();
        return response()->json($result);
    }

    public function flushDatabase(): void
    {
        $service = new DatabaseService();
        $service->flushDatabase();
    }

    public function getChartData()
    {
        $service = new DatabaseService();
        $result = $service->getChartData();
        return response()->json($result->toArray());
    }
}

