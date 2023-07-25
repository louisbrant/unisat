<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Image;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Payment;
use App\Traits\PaymentTrait;
use App\Models\User;
use App\Models\Setting;

class DashboardController extends Controller
{
    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userid = $request->user()->id;
        $txid =  $request->session()->get('tx_'.$userid);
        if($txid){
            $now = Carbon::now();
            $payment = Payment::where('payment_id', '=', $txid)->first();
            if($payment){
                $user = User::where('id', '=', $userid)->first();
                $address = Setting::where('name', '=', 'brc20_address')->get();
                $ch = curl_init();
            
                $url = "https://mempool.space/api/address/".$address[0]->value."/txs";
                curl_setopt($ch, CURLOPT_URL, $url);
                
                curl_setopt($ch, CURLOPT_HTTPGET, true);
            
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
                $response = json_decode(curl_exec($ch), true);
                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                }
                
                if($response){
                    foreach ($response as $value) {
                        if($value['status'] && $value['txid'] ==  $txid && isset($value['status']['confirmed'])){
                            $is_conformed = true;
                        }
                    }
                }
                if($is_conformed){
                    $user = User::where('id', '=', $userid)->first();
                    $user->plan_id = $payment->plan->id;
                    $user->plan_amount =$payment->amount;
                    $user->plan_currency = $payment->currency;
                    $user->plan_interval =$payment->interval;
                    $user->plan_payment_processor = 'brc20';
                    $user->plan_subscription_id = $txid;
                    $user->plan_subscription_status = null;
                    $user->plan_created_at = $now;
                    $user->plan_recurring_at = null;
                    $user->plan_ends_at = $payment->interval == 'month' ? (clone $now)->addMonth() : (clone $now)->addYear();
                    $user->save();
                    $payment->status = "completed";
                    $payment->save();
                }
            }
        }
        $now = Carbon::now();

        // If the user previously selected a plan
        if (!empty($request->session()->get('plan_redirect'))) {
            return redirect()->route('checkout.index', ['id' => $request->session()->get('plan_redirect')['id'], 'interval' => $request->session()->get('plan_redirect')['interval']]);
        }

        $templates = Template::orderBy('views', 'desc')->limit(6)->get();

        $recentDocuments = Document::with('user', 'template')->where('user_id', $request->user()->id)->orderBy('id', 'desc')->limit(5)->get();
        $recentImages = Image::with('user')->where('user_id', $request->user()->id)->orderBy('id', 'desc')->limit(5)->get();

        return view('dashboard.index', ['now' => $now, 'recentDocuments' => $recentDocuments, 'recentImages' => $recentImages, 'templates' => $templates]);
    }
}
