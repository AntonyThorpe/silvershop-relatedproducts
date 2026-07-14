# Installation & Configuration of SilverShop Related Products

## Installation
* In a terminal:
`composer require antonythorpe/silvershop-relatedproducts`
* dev/build

A new tab `Related` will appear within each product.

## Templates
In your `{yourtheme/app}/templates/SilverShop/Page/Layout/Product.ss` add `<% include AntonyThorpe\SilverShopRelatedProducts\RelatedProducts %>` under the content.

To customise, copy `vendor/antonythorpe/silvershop-relatedproducts/templates/AntonyThorpe/SilverShopRelatedProducts/RelatedProducts.ss` to your `{yourtheme/app}/templates/AntonyThorpe/SilverShopRelatedProducts/` folder and adjust as needed.

Optional: in your `{yourtheme/app}/templates/SilverShop/includes/ProductGroupItem.ss` add
```html
<% if $RelatedTitle %>
    <h3>$RelatedTitle</h3>
<% end_if %>
```
to capture the Related Title against each listed related product.
