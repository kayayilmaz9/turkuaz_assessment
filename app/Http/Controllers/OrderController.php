<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class OrderController extends Controller
{
    public function store(Request $request)
    {

        $rules = array(
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        );
        $messages = array(
            'products.required' => '- Products dizisini göndermeniz gereklidir',
            'products.array' => '- Products dizi olmalıdır',
            'products.*.id.required' => '- id parametresi gönderilmelidir',
            'products.*.id.exists' => '- Gönderilen ürün id sistemdeki ürünlerle eşleşmiyor',
            'products.*.quantity.required' => '- quantity parametresi gönderilmelidir',
            'products.*.quantity.integer' => '- quantity parametresi integer tipinde olmalıdır',
            'products.*.quantity.min' => '- quantity parametresi en az 1 olmalıdır'
        );

        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $token = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $token);
        $user = User::where('api_token', $token)->first();

        $products = $request->input('products');

        $total = 0;
        $items = [];

        try {
            foreach ($products as $productData) {
                $product = Product::find($productData['id']);
                if ($product->stock < $productData['quantity']) {
                    return response()->json(['error' => $product->name.' isimli ürün için yeterli stok bulunmamaktadır. '], 400);
                }


                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price,
                ];

                $total += $product->price * $productData['quantity'];
            }

            $shipping = $total > 50 ? 0 : 10;

            $discountList = $this->applyCampaign($items, $total);
            if(isset($discountList) && !empty($discountList)){
                $discount = max($discountList);
                $campaign_id = array_search($discount, $discountList);
            }else{
                $discount = 0;
                $campaign_id = 0;
            }

            $final_amount = $total + $shipping - $discount;

            $order = new Order();
            $order->user_id = $user->id;
            $order->order_code = Str::uuid()->toString();
            $order->amount = $total;
            $order->discount_amount = $discount;
            $order->campaign_id = $campaign_id;
            $order->shipping_amount = $shipping;
            $order->final_amount = $final_amount;

            if($order->save()){
                foreach ($items as $item) {
                    $order_item = new OrderItem();
                    $order_item->order_id = $order->id;
                    $order_item->product_id = $item['product_id'];
                    $order_item->quantity = $item['quantity'];
                    $order_item->price = $item['price'];

                    if($order_item->save()){
                        $productFinal = Product::find($item['product_id']);
                        $productFinal->stock -= $item['quantity'];
                        $productFinal->save();
                    }
                }

                return response()->json($order, 201);
            }else{
                return response()->json(['error' => 'Sipariş kaydedilirken bir hata oluştu.'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Sipariş kaydedilirken bir hata oluştu.', 'message' => $e->getMessage()], 500);
        }

    }

    private function applyCampaign($items, $total)
    {

        $campaigns = Campaign::all();
        $discountList = [];

        if ($campaigns->isNotEmpty()) {
            foreach ($campaigns as $campaign) {
                $discount = 0;
                $campaignConditions = json_decode($campaign->conditions,true);
                switch ($campaign->discount_type) {
                    case 'free_product':
                        $authorCondition = $campaignConditions['author'];
                        $minQuantity = $campaignConditions['min_quantity'];
                        $maxFree = $campaignConditions['max_free'];
                        $eligibleItems = array_filter($items, function ($item) use ($authorCondition) {
                            $product = Product::find($item['product_id']);
                            return $product->author === $authorCondition;
                        });
                        $eligibleCount = array_reduce($eligibleItems, function ($carry, $item) {
                            return $carry + $item['quantity'];
                        }, 0);

                        if ($eligibleCount >= $minQuantity) {
                            $freeItemsCount = intdiv($eligibleCount, $minQuantity);
                            $freeItemsCount = min($freeItemsCount, $maxFree);

                            $freeItemPrice = min(array_column($eligibleItems, 'price'));
                            $discount = $freeItemPrice * $freeItemsCount;
                        }
                        break;

                    case 'product_based_percentage':
                        $authorCondition = $campaignConditions['author_type'];
                        $eligibleItems = array_filter($items, function ($item) use ($authorCondition) {
                            $product = Product::find($item['product_id']);
                            return $product->author_type === $authorCondition;
                        });
                        $eligibleTotal = array_reduce($eligibleItems, function ($carry, $item) {
                            return $carry + $item['price'] * $item['quantity'];
                        }, 0);

                        $discount = $eligibleTotal * ($campaign->discount_value / 100);
                        break;

                    case 'total_amount_percentage':
                        $minTotal = $campaignConditions['min_total'];
                        if ($total >= $minTotal) {
                            $discount = $total * ($campaign->discount_value / 100);
                        }
                        break;
                }

                if($discount != 0){
                    $discountList[$campaign->id]=$discount;
                }
            }
        }

        return $discountList;
    }

    public function show($order_code)
    {
        $order = Order::with(['items','campaign'])->where('order_code',$order_code)->first();
        return response()->json($order);
    }
}
