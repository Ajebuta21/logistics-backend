<?php

namespace App\Http\Controllers;

use App\Jobs\SendShipmentEmail;
use App\Models\Package;
use App\Models\TrackingHistory;
use App\Providers\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function createPackage(Request $request)
    {
        $validatedData = $request->validate([
            'senders_name' => 'required',
            'senders_email' => 'required|email',
            'senders_number' => 'required',
            'recievers_name' => 'required',
            'recievers_email' => 'required|email',
            'recievers_number' => 'required',
            'origin' => 'required',
            'destination' => 'required',
            'distance' => 'required',
            'time_taken' => 'required',
            'weight' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);


        $tracking_id = $this->generateNumber();

        $package = new Package([
            'senders_name' => $validatedData['senders_name'],
            'senders_email' => $validatedData['senders_email'],
            'senders_number' => $validatedData['senders_number'],
            'recievers_name' => $validatedData['recievers_name'],
            'recievers_email' => $validatedData['recievers_email'],
            'recievers_number' => $validatedData['recievers_number'],
            'origin' => $validatedData['origin'],
            'destination' => $validatedData['destination'],
            'distance' => $validatedData['distance'],
            'time_taken' => $validatedData['time_taken'],
            'weight' => $validatedData['weight'],
            'description' => $validatedData['description'],
            'tracking_id' => $tracking_id,
            'price' => $validatedData['price'],
        ]);

        $history = new TrackingHistory([
            'location' => "Shipment created",
            'package_id' => $tracking_id,
        ]);

        $package->save();
        $history->save();

        return response()->json(['message' => 'Shipment has been created', 'package' => $package], 201);
    }

    private function generateNumber()
    {
        $prefix = 'Mockup-';
        $randomNumber = mt_rand(100000000, 999999999);
        return $prefix . $randomNumber;
    }

    public function getPackage($tracking_id)
    {
        $package = Package::where('tracking_id', $tracking_id)->first();

        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        $history = TrackingHistory::where('package_id', $tracking_id)->get();

        return response()->json(['package' => $package, 'history' => $history], 200);
    }

    public function getAllPAckages()
    {
        $packages = Package::all();

        return response()->json($packages, 200);
    }

    public function changeStatus(Request $request)
    {
        $validatedData = $request->validate([
            'tracking_id' => 'required',
            'status' => 'required',
        ]);

        $package = Package::where('tracking_id', $validatedData['tracking_id'])->first();
        if ($package->status === $validatedData['status']) {
            return response()->json(['message' => 'Status is already set to ' . $validatedData['status'] . ''], 403);
        }
        $package->status = $validatedData['status'];
        $package->save();
        if ($validatedData['status'] === 'in transit') {
            $mailService = new MailService();
            $to = $package->recievers_email;
            $subject = 'Shipping order in transit';
            $body = '<body
                    style="
                    width: 100vw;
                    height: fit-content;
                    padding: 0;
                    margin: 0;
                    background-color: white;
                    display: block;
                    font-family: Arial, Helvetica, sans-serif;
                    "
                >
                    <div style="height: 100; width: 100%; display: block"></div>
                    <h2 style="color: #dc2626; width: 100%; text-align: center">
                    Order in transit
                    </h2>
                    <div style="width: 80vw; line-height: 20px; margin: auto">
                    <p>Hello there,</p>
                    <p>
                        Your package is been shipped to you. The details of your package are as follows:
                    </p>
                    <p>Tracking ID: ' . $package->tracking_id . '.</p>
                    <p>From: ' . $package->origin . '.</p>
                    <p>To: ' . $package->destination . '.</p>
                    <p>Sender: ' . $package->senders_name . '.</p>
                    <p>Senders number: ' . $package->senders_number . '.</p>
                    <p>Reciever: ' . $package->recievers_name . '.</p>
                    <p>Recievers number: ' . $package->recievers_number . '.</p>
                    <p>Package description: ' . $package->description . '.</p>
                    <p>Weight: ' . $package->weight . 'lbs.</p>
                    <p>Package will arrive in: ' . $package->time_taken . ' days.</p>
                    <p>Shipping cost: $' . $package->price . '.</p>
                    </div>
                </body>';

            $mailService->sendMail($to, $subject, $body);
        }
        return response()->json(['message' => "Status changed"], 201);
    }

    public function createHistory(Request $request)
    {
        $validatedData = $request->validate([
            'package_id' => 'required',
            'location' => 'required'
        ]);

        $package = Package::where('tracking_id', $validatedData['package_id'])->first();

        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        $history = new TrackingHistory([
            'location' => $validatedData['location'],
            'package_id' => $validatedData['package_id'],
        ]);

        $history->save();

        return response()->json(['message' => "History added"], 201);
    }

    public function deletePackage(Request $request)
    {
        $package = Package::where('tracking_id', $request->tracking_id)->first();

        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        $package->delete();

        return response()->json(['message' => "Package deleted"], 201);
    }
}
