# Introduction

This is an interview question that [SahelBooks](https://www.sahelbooks.com/) Company send it to me to solve.


## Installation

Clone the repository:
```bash
git clone 'repo_link'
```
Install all dependencies with [composer](https://getcomposer.org/).

```bash
composer install
```

## Run Program


Go to project root and run.

```bash
cp .env.example .env
```
then followed by these [artisan](https://laravel.com/docs/10.x/artisan) commands.
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Design and Architecture

I used Facade Design Pattern to organize CRUD operations on Product Model & to manage purchasing products.

```
app
---- Facades
------------ ProductFacade.php
---- Service
------------ ProductService.php
------------ Service.php
---- Helpers
------------ helpers.php
---- Providers
------------ FacadeServiceProvider.php
---- Http
------------ Controllers
------------------------ ProductController.php
------------ Constants
------------------------ Constant.php
------------ Requests
------------------------ StoreProductRequest.php
------------------------ EditProductRequest.php
------------------------ PurchaseProductRequest.php
------------ Resources
------------------------ ProductResource.php
...
..
.

```
In api.php:
``` php
Route::group(['prefix' => 'products'], function() {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/purchase', [ProductController::class, 'purchase']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});
```
The solution of the problem is in method "purchase"

ProductController.php:
``` php
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
```
This request form accept products as Array of product ids:
PurchaseItemsRequest.php:
``` php
    public function rules(): array
    {
        return [
            'products' => 'required|array',
        ];
    }
```  

## Input & Output

Input in [Postman](https://www.postman.com/) as array of product ids like:
```
products: [
       1
       2
       3
       4
       etc
]

```
When Input contain the following products:
```
T-shirt
Blouse
Pants
Shoes
Jacket
```
The output is:
```
Subtotal: $386.95
Shipping: $110
VAT: $54.173
Discount: 
		10% off shoes: -$7.999
		50% off jacket: -$99.995
		$10 off shipping: -$10
Total: $433.129
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

