<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    /**
     * @param * @param Request $request
     * @return array
     * @throws
     * @created by adarshepep@gmail.com on (08 Jan 2020 at 6:08 PM)
     */
    public function fetchData(Request $request)
    {

        $picup_location_id = $request->picup_location;
        $destination_location_id = $request->destination_location;
        $time = $request->time;
        $tripType = $request->trip_type;

        $occuranceId = DB::table('occurance_timetable')
            ->where('location_id', $picup_location_id)
            ->orWhere('location_id', $destination_location_id);

        //Services with type 1
        if ($picup_location_id && $destination_location_id && $time && $tripType == 1) {

            (clone $occuranceId)->whereTime('time', '<', Date::parse($time)->subHours(3));
            $output = [];

            foreach ($occuranceId->cursor() as $occurance) {

                $serviceId = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->first()->service_id;

                $service = DB::table('services')
                    ->where('service_id', $serviceId)->first();

                $picUpTime = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->where('location_id', $picup_location_id)
                    ->first()->occurance_name;

                $arrivalTime = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->where('location_id', $destination_location_id)
                    ->first()->occurance_name;

                $accessories = DB::table('service_accessories')
                    ->where('service_id', $serviceId)
                    ->get();

                $seatsAvailable = DB::table('vehicles')
                    ->where('vehicle_id', $service->vehicle_id)->first()->no_of_seats;

                $output[] = [
                    "service_name" => $service->service_name,
                    "pickup_time" => $picUpTime,
                    "arrival_time" => $arrivalTime,
                    "accessories" => $accessories,
                    "seats" => $seatsAvailable
                ];
            }

            return $output;
        }

        //Services with type 2

        if ($picup_location_id && $destination_location_id && $time && $tripType == 2) {

            (clone $occuranceId)->whereTime('time', '<', Date::parse($time)->addHour());
            $output = [];

            foreach ($occuranceId->cursor() as $occurance) {

                $serviceId = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->first()->service_id;

                $service = DB::table('services')
                    ->where('service_id', $serviceId)->first();

                $picUpTime = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->where('location_id', $picup_location_id)
                    ->first()->occurance_name;

                $arrivalTime = DB::table('occurances')
                    ->where('occurance_id', $occurance)
                    ->where('location_id', $destination_location_id)
                    ->first()->occurance_name;

                $accessories = DB::table('service_accessories')
                    ->where('service_id', $serviceId)
                    ->get();

                $seatsAvailable = DB::table('vehicles')
                    ->where('vehicle_id', $service->vehicle_id)->first()->no_of_seats;

                $output[] = [
                    "service_name" => $service->service_name,
                    "pickup_time" => $picUpTime,
                    "arrival_time" => $arrivalTime,
                    "accessories" => $accessories,
                    "seats" => $seatsAvailable
                ];
            }

            return $output;
        }
    }
}
