<?php

namespace App\Http\Controllers;

use App\Http\Constants\Constant;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\PurchaseItemsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ProductFacade;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $query = request()->all();
        try 
        {
            $products = ProductFacade::getList($query);
            $count = $products->count();

            return successResponse(ProductResource::collection($products), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        $data = $request->validated();
        try 
        {
            $product = ProductFacade::store($data);
            $count = ProductFacade::getCount();

            DB::commit();
            return successResponse(new ProductResource($product), $count, "created successfully", 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function show(string $product): JsonResponse
    {
        try 
        {
            $product = ProductFacade::getSingle($product);
            $count = ProductFacade::getCount();

            DB::commit();
            return successResponse(new ProductResource($product), $count);
        }
        catch(\Exception $e)
        {
            return failResponse($e->getMessage());
        }
    }

    public function update(EditProductRequest $request, string $product): JsonResponse
    {
        DB::beginTransaction();
        $data = $request->validated();
        try 
        {
            $product = ProductFacade::getSingle($product);

            $product = ProductFacade::edit($data, $product);
            $count = ProductFacade::getCount();

            DB::commit();
            return successResponse(new ProductResource($product), $count, "updated successfully");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }

    public function destroy(string $product): JsonResponse
    {
        DB::beginTransaction();
        try 
        {
            $product = ProductFacade::getSingle($product);
            
            ProductFacade::delete($product);

            DB::commit();
            return successResponse(message: "deleted successfully");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return failResponse($e->getMessage());
        }
    }
    

    public function purchase(PurchaseItemsRequest $request)
    {
        DB::beginTransaction();
        $data = $request->validated();
        try 
        {
            $products = ProductFacade::getList(['ids' => $data['products']]);
            
            $subTotal = ProductFacade::getSubTotal($products);

            $shipping = ProductFacade::getShipping($products);

            $VAT = ProductFacade::getVAT($products);

            $invoice = "Subtotal: " . config('app.currency') . $subTotal. "\n"
                        . "Shipping: " . config('app.currency') . $shipping. "\n"
                        . "VAT: ". config('app.currency') . $VAT . "\n";

            $total = 0;
            if(ProductFacade::hasDiscount($products))
            {
                $shoesOffer = 0; $jacketOffer = 0; $shippingOffer = 0;
                [
                    $shoesOffer, 
                    $jacketOffer, 
                    $shippingOffer
                ] = ProductFacade::getDetailedDiscount(
                    $products, 
                    $shoesOffer, 
                    $jacketOffer, 
                    $shippingOffer
                );

                $invoice = $invoice . "Discount: \n";

                if($shoesOffer != 0)
                    $invoice = $invoice . "\t\t10% off shoes: ". '-' . config('app.currency') .$shoesOffer. "\n";

                if($jacketOffer != 0)
                    $invoice = $invoice . "\t\t50% off jacket: ". '-' . config('app.currency') .$jacketOffer . "\n";

                if($shippingOffer != 0)
                    $invoice = $invoice . "\t\t$10 off shipping: " . '-' . config('app.currency') .$shippingOffer ."\n";

                $total = $subTotal + $shipping + $VAT - $shippingOffer - $jacketOffer - $shoesOffer;
            }
            else
                $total = $subTotal + $shipping + $VAT;

            $invoice = $invoice . "Total: ". config('app.currency') .$total;

            return $invoice;
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
