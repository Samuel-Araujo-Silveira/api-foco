<?php

namespace App\Services;
use XMLReader;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Rate;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\ReservationRoom;
use App\Models\GuestCount;
use App\Models\RateReservationRoom;
use Illuminate\Support\Facades\Log;


class XmlImportService
{
    private string $basePathToXMls;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->basePathToXMls = storage_path('app/private/xml');
    }

    public function importAllXmlFiles(): void
    {
        Log::info('XML import started');
        $this->importHotels();
        $this->importRooms();
        $this->importRates();
        $this->importReservations();
        Log::info('XML import finished');
    }

    public function importHotels(): void
    {
        $this->importXml('hotels.xml', 'hotel', function ($attributesOfTheObject, $contentOfTheObject) {
            Hotel::updateOrCreate(
                ['id'   => $attributesOfTheObject['id']],
                ['name' => $contentOfTheObject['name']]
            );
        });
    }

    public function importRooms(): void
    {
        $this->importXml('rooms.xml', 'room', function ($attributesOfTheObject, $contentOfTheObject) {
            Room::updateOrCreate(
                ['id'              => $attributesOfTheObject['id']],
                [
                    'hotel_id'        => $attributesOfTheObject['hotel_id'],
                    'name'            => $contentOfTheObject['room'],
                    'inventory_count' => $attributesOfTheObject['inventory_count'],
                    'hotel_name'      => $attributesOfTheObject['hotel_name']
                ]
            );
        });
    }

    public function importRates(): void
    {
        $this->importXml('rates.xml', 'rate', function ($attributes, $children) {
            Rate::updateOrCreate(
                ['id'       => $attributes['id']],
                [
                    'hotel_id' => $attributes['hotel_id'],
                    'name'     => $attributes['hotel_name'],
                    'active'   => $attributes['active'],
                    'price'    => $attributes['price'],
                ]
            );
        });
    }

    public function importReservations(): void
    {
        $reservations = $this->importReservationsXml();
        foreach ($reservations as $data) {
            $customer = Customer::updateOrCreate([
                'first_name' => $data['customer']['first_name'],
                'last_name'  => $data['customer']['last_name'],
            ]);

            $reservationModel = Reservation::updateOrCreate(
                ['id' => $data['reservation']['id']],
                [
                    'date'        => $data['reservation']['date'],
                    'time'        => $data['reservation']['time'],
                    'hotel_id'    => $data['reservation']['hotel_id'],
                    'customer_id' => $customer->id,
                ]
            );

            $reservationRoom = ReservationRoom::updateOrCreate(
                ['id' => $data['room']['id']],
                [
                    'arrival_date'   => $data['room']['arrival_date'],
                    'departure_date' => $data['room']['departure_date'],
                    'currencycode'   => $data['room']['currencycode'],
                    'meal_plan'      => $data['room']['meal_plan'],
                    'totalprice'     => $data['room']['totalprice'],
                    'room_id'        => $data['room']['room_id'],
                    'reservation_id' => $data['reservation']['id'],
                ]
            );

            foreach ($data['guest_counts'] as $guestCount) {
                GuestCount::updateOrCreate(
                    [
                        'reservation_room_id' => $reservationRoom->id,
                        'type'                => $guestCount['type'],
                    ],
                    ['count' => $guestCount['count']]
                );
            }

            foreach ($data['prices'] as $price) {
                RateReservationRoom::updateOrCreate(
                    [
                        'reservation_room_id' => $reservationRoom->id,
                        'rate_id'             => $price['rate_id'],
                        'date'                => $price['date'],
                    ],
                    ['amount' => $price['amount']]
                );
            }
        }
    }

    private function importXml(string $xmlFileName, string $xmlFileTag, callable $persist ): void
    {
        $xmlReader = new XMLReader();
        $xmlReader->open($this->basePathToXMls . '/' . $xmlFileName);

        while($xmlReader->read()) {

            if($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name === $xmlFileTag) {

                $attributesOfTheObject = [];
                if ($xmlReader->hasAttributes) {
                    while ($xmlReader->moveToNextAttribute()) {
                        $attributesOfTheObject[$xmlReader->name] = $xmlReader->value;
                    }
                    $xmlReader->moveToElement();
                }

                $contentOfTheObject = [];
                $innerReader = new XMLReader();
                $innerReader->xml($xmlReader->readOuterXml());
                while ($innerReader->read()) {
                    if ($innerReader->nodeType == XMLReader::ELEMENT) {
                        $contentOfTheObject[$innerReader->name] = $innerReader->readString();
                    }
                }

                $innerReader->close();
                $persist($attributesOfTheObject, $contentOfTheObject);
            }
        }
        $xmlReader->close();
    }


    private function importReservationsXml(): array
    {
        $reservations = [];

        $xmlReader = new XMLReader();
        $xmlReader->open($this->basePathToXMls . '/reservations.xml');

        while ($xmlReader->read()) {
            if ($xmlReader->nodeType == XMLReader::ELEMENT && $xmlReader->name === 'reservation') {
                $reservation = simplexml_load_string($xmlReader->readOuterXml());
                $room = $reservation->room;

                $prices = [];
                foreach ($room->price as $price) {
                    $prices[] = [
                        'rate_id' => (string) $price['rate_id'],
                        'date'    => (string) $price['date'],
                        'amount'  => (float)  $price,
                    ];
                }

                $guestCounts = [];
                foreach ($room->guest_counts->guest_count as $guestCount) {
                    $guestCounts[] = [
                        'type'  => (string) $guestCount['type'],
                        'count' => (int)    $guestCount['count'],
                    ];
                }

                $reservations[] = [
                    'customer' => [
                        'first_name' => (string) $reservation->customer->first_name,
                        'last_name'  => (string) $reservation->customer->last_name,
                    ],
                    'reservation' => [
                        'id'       => (int)    $reservation->id,
                        'date'     => (string) $reservation->date,
                        'time'     => (string) $reservation->time,
                        'hotel_id' => (int)    $reservation->hotel_id,
                    ],
                    'room' => [
                        'id'             => (int)    $room->roomreservation_id,
                        'arrival_date'   => (string) $room->arrival_date,
                        'departure_date' => (string) $room->departure_date,
                        'currencycode'   => (string) $room->currencycode,
                        'meal_plan'      => (string) $room->meal_plan,
                        'totalprice'     => (float)  $room->totalprice,
                        'room_id'        => (int)    $room->id,
                    ],
                    'guest_counts' => $guestCounts,
                    'prices'       => $prices,
                ];
            }
        }

        $xmlReader->close();

        return $reservations;
    }

}

