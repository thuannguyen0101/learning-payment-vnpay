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

        if ($row->qty < 100 && $request->qty < 100){
            Cart::update($request->rowId,$row->qty = $request->qty);
        }
        else{
            return redirect('/test/order')->with('message','lỗi');
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
        foreach (Cart::content() as $item) {
            $order_detail = new Orderdetail();
            $order_detail->order_id = $order->id;
            $order_detail->product_id = $item->id;
            $order_detail->unit_price = $item->subtotal();
            $order_detail->quantity = $item->qty;
            $order_detail->save();
        }

        session(['cost_id' => $request->id]);
        session(['url_prev' => url()->previous  ()]);
        $vnp_TmnCode = "UDOPNWS1";
        $vnp_HashSecret = "EBAHADUGCOEWYXCMYZRMTMLSHGKNRPBN";
        $vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8000/return-vnpay";
        $vnp_TxnRef = date("YmdHis");
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dich vụ";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->input('amount') * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.0.0",
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
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
            $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    public function response(){

    }

}
