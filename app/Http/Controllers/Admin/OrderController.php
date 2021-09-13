<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Payer;

class  OrderController extends Controller
{
    public function addToCart($id)
    {
        $product = Product::find($id);
        $floatVar = (float)$product->price;
        Cart::add($product->id, $product->name, 1, $floatVar, 100, ['thumbnail' => $product->thumbnail]);
    }

    public function index()
    {
        Cart::add(1, 'Bí đỏ hồ lô', 5, 9750, 100, ['thumbnail' => 'http://res.cloudinary.com/quando213/image/upload/v1630746030/gatgatexpress/z2397714459635_678eba187af4f0c9fea558852ab9601e_761390139f014619820f7dc1e892ad68_1024x1024_rejmtx.jpg']);
        return view('Admin.Order.list', [
            'title' => 'Product',
            'breadcrumb' => 'Edit Product',
            'data' => ''
        ]);
    }

    public function test()
    {
        $cart = Cart::content();
        $lists = Product::all();
        return view('test_order', [
            'lists' => $lists,
            'cart' => $cart
        ]);
    }

    public function detail()
    {
        return view('list');
    }

    public function update(Request $request)
    {
        $row = Cart::get($request->rowId);

        if ($row->qty < 100 && $request->qty < 100) {
            Cart::update($request->rowId, $row->qty = $request->qty);
        } else {
            return redirect('/test/order')->with('message', 'lỗi');
        }
        return redirect('/test/order');
    }

    public function remove($rowId)
    {
        Cart::remove($rowId);
        return redirect('/test/order');
    }

    public function buynow(Request $request)
    {
        $order = new Order();
        $order->total_price = Cart::total();
        $floatVar = floatval(preg_replace("/[^-0-9\.]/", "", Cart::total()));
        $order->user_id = 1;
        $order->shipping_name = 'Nguyễn Ngọc Thuận';
        $order->shipping_district_id = 1;
        $order->shipping_ward_id = 1;
        $order->shipping_street = 'Số nhà 14 ngách 28 ngõ 52 đường Mỹ Đình';
        $order->shipping_phone = '0929427881';
        $order->shipper_id = 1;
        $order->payment_method = false;
        $order->created_at = Carbon::now();
        $order->updated_at = Carbon::now();
        $order->status = 1;
        $order->save();

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "https://sem2-project.herokuapp.com/response";
        $vnp_TmnCode = "OV95A0Y9";
        $vnp_HashSecret = "ZGZKUWRMIPLAZFFGCMMRDRTQUKFOMGLS";
        $vnp_TxnRef = $order->id;
        $vnp_OrderInfo = "Thanh toan don hang ";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $floatVar * 100;
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            'vnp_BankCode' => $vnp_BankCode
        );
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    public function response(Request $request)
    {
        return $request;
    }

    public function ipnResponse(Request $request)
    {
        Log::debug('An informational message.');
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_HashSecret = "ZGZKUWRMIPLAZFFGCMMRDRTQUKFOMGLS";
        $inputData = array();
        $returnData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_Amount = $inputData['vnp_Amount'] / 100;
        $order = Order::find($request->vnp_TxnRef);
        $floatVar = floatval(preg_replace("/[^-0-9\.]/", "", $order->total_price));
        try {
            if ($secureHash === $vnp_SecureHash) {
                if ($order != NULL) {
                    if ($floatVar == $vnp_Amount) {
                        if ($order->payment_method != NULL && $order->payment_method == 0) {
                            if ($request->vnp_ResponseCode == '00' || $request->vnp_TransactionStatus == '00') {
                                $order->update(['payment_method' => true]);
                                $order->save();
                                $returnData['RspCode'] = '00';
                                $returnData['Message'] = 'Confirm Success';
                                return $returnData;
                            } else {
                                $Status = 2;
                            }
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                            return $returnData;
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'invalid amount';
                        return $returnData;
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                    return $returnData;
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
                return $returnData;
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
    }
    public function testIpn($id)
    {
        $order = Order::find($id);
        return $order;
    }
}
