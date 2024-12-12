<?php

namespace App\Jobs;

use App\Models\Package;
use App\Providers\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendShipmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $package;
    protected $tracking_id;
    protected $validatedData;

    public function __construct(Package $package, $tracking_id, $validatedData)
    {
        $this->package = $package;
        $this->tracking_id = $tracking_id;
        $this->validatedData = $validatedData;
    }

    public function handle()
    {
        $mailService = new MailService();
        $to = $this->validatedData['senders_email'];
        $subject = 'Order created';
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
                    <img
                    src="globemergelogistics.com/gml.png"
                    style="width: 100px; margin: 50px auto; display: block"
                    />
                    <h2 style="color: #dc2626; width: 100%; text-align: center">
                    Shipping Order Created
                    </h2>
                    <div style="width: 80vw; line-height: 20px; margin: auto">
                    <p>Hello there,</p>
                    <p>
                        Your package has been created and is awaiting payment confirmation to
                        begin shipping. The details of your package are as follows:
                    </p>
                    <p>Tracking ID: ' . $this->tracking_id . '.</p>
                    <p>From: ' . $this->validatedData['origin'] . '.</p>
                    <p>To: ' . $this->validatedData['destination'] . '.</p>
                    <p>Sender: ' . $this->validatedData['senders_name'] . '.</p>
                    <p>Senders number: ' . $this->validatedData['senders_number'] . '.</p>
                    <p>Reciever: ' . $this->validatedData['recievers_name'] . '.</p>
                    <p>Recievers number: ' . $this->validatedData['recievers_number'] . '.</p>
                    <p>Package description: ' . $this->validatedData['description'] . '.</p>
                    <p>Weight: ' . $this->validatedData['weight'] . 'lbs.</p>
                    <p>Package will arrive in: ' . $this->validatedData['time_taken'] . ' days.</p>
                    <p>Shipping cost: ' . $this->validatedData['price'] . ' days.</p>
                    <p>
                        Thank you for choosing GlobeMerge Logistics. For more enquires, please
                        contact our customer support through this channel: <br />
                        Email: <span style="color: #dc2626">support@globemergelogistics.com</span>.
                    </p>
                    </div>
                </body>';

        $mailService->sendMail($to, $subject, $body);
    }
}
