<?php

namespace Tests\Unit\Services\Shipment;

use App\DTOs\Admin\UpdateShipmentStatusDTO;
use App\DTOs\User\CreateShipmentDTO;
use App\Enums\ShipmentStatusEnum;
use App\Http\Resources\Shipment\ShipmentResource;
use App\Jobs\HandleSystemLoggingJob;
use App\Models\Shipment;
use App\Models\User;
use App\Repositories\Shipment\ShipmentRepositoryInterface;
use App\Services\Geolocation\GeolocationServiceInterface;
use App\Services\Logging\LoggingService;
use App\Services\Shipment\ShipmentService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\TestCase;

class ShipmentServiceTest extends TestCase
{
    private ShipmentService $shipmentService;
    private ShipmentRepositoryInterface $mockShipmentRepository;
    private LoggingService $mockLoggingService;
    private GeolocationServiceInterface $mockGeolocationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockShipmentRepository = Mockery::mock(ShipmentRepositoryInterface::class);
        $this->mockLoggingService = Mockery::mock(LoggingService::class);
        $this->mockGeolocationService = Mockery::mock(GeolocationServiceInterface::class);

        $this->shipmentService = new ShipmentService(
            $this->mockShipmentRepository,
            $this->mockLoggingService,
            $this->mockGeolocationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_generates_unique_tracking_number()
    {
        $trackingNumber = $this->shipmentService->generateTrackingNumber();

        $this->assertIsString($trackingNumber);
        $this->assertTrue(Str::isUuid($trackingNumber));
    }

    /** @test */
    public function it_creates_shipment_successfully()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getId')->andReturn(1);
        $user->shouldReceive('setAttribute')->zeroOrMoreTimes();
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(CreateShipmentDTO::class);
        $dto->shouldReceive('toShipmentData')->andReturn([
            'sender_name' => 'John Mark',
            'receiver_name' => 'Sane Johnson',
            'origin_address' => 'Yaba, Lagos',
            'destination_address' => 'Berger Bus Stop, Lagos',
            'origin_latitude' => null,
            'origin_longitude' => null,
            'destination_latitude' => null,
            'destination_longitude' => null,
            'created_by' => 1,
        ]);
        $dto->createdBy = $user;
        $dto->originAddress = 'Yaba, Lagos';
        $dto->destinationAddress = 'Berger Bus Stop, Lagos';

        $ipAddress = '127.0.0.1';

        $originCoordinates = ['latitude' => 40.7128, 'longitude' => -74.0060];
        $destinationCoordinates = ['latitude' => 34.0522, 'longitude' => -118.2437];

        $this->mockGeolocationService
            ->shouldReceive('getCoordinates')
            ->with('123 Main St')
            ->andReturn($originCoordinates);

        $this->mockGeolocationService
            ->shouldReceive('getCoordinates')
            ->with('456 Oak Ave')
            ->andReturn($destinationCoordinates);

        $shipmentRecord = Mockery::mock(Shipment::class)->makePartial();
        $shipmentRecord->shouldReceive('load')->with('user')->andReturnSelf();
        $shipmentRecord->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $shipmentRecord->shouldReceive('getAttribute')->with('tracking_number')->andReturn('test-uuid');
        $shipmentRecord->shouldReceive('setAttribute')->zeroOrMoreTimes();

        $this->mockShipmentRepository
            ->shouldReceive('createShipment')
            ->once()
            ->andReturn($shipmentRecord);

        Log::shouldReceive('info')->times(2);

        // Act
        $result = $this->shipmentService->createShipment($dto, $ipAddress);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Shipment created successfully', $result['message']);
        $this->assertInstanceOf('App\Http\Resources\Shipment\ShipmentResource', $result['data']);

    }

    /** @test */
    public function it_handles_shipment_creation_failure()
    {
        // Arrange
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getId')->andReturn(1);
        $user->id = 1;

        $dto = Mockery::mock(CreateShipmentDTO::class);
        $dto->shouldReceive('toShipmentData')->andReturn([
            'sender_name' => 'John Mark',
            'receiver_name' => 'Sane Johnson',
            'origin_address' => 'Yaba, Lagos',
            'destination_address' => 'Berger Bus Stop, Lagos',
            'origin_latitude' => null,
            'origin_longitude' => null,
            'destination_latitude' => null,
            'destination_longitude' => null,
            'created_by' => 1,
        ]);
        $dto->createdBy = $user;
        $dto->originAddress = 'Yaba, Lagos';
        $dto->destinationAddress = 'Berger Bus Stop, Lagos';

        $request = 'test-request-string';

        $this->mockGeolocationService
            ->shouldReceive('getCoordinates')
            ->andReturn(['latitude' => 40.7128, 'longitude' => -74.0060]);

        $this->mockShipmentRepository
            ->shouldReceive('createShipment')
            ->once()
            ->andReturn(null);

        Log::shouldReceive('info')->once();
        Log::shouldReceive('warning')->once();

        // Act
        $result = $this->shipmentService->createShipment($dto, $request);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('Shipment record not created', $result['message']);
    }
}
