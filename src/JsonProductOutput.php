<?php
namespace fruity;

class JsonProductOutput implements ProductOutputInterface
{

    public function render(array $products)
    {
        if (!is_array($products)) {
            throw new \Exception("Invalid set of products");
        }

        $total = 0.0;

        foreach($products as $product) {
            /* @var \fruity\Product $product */
            $item['title'] = $product->getTitle();
            $item['size'] = $product->getBytesReadInkb();
            $item['unit_price'] = $product->getUnitPrice();
            $item['description'] = $product->getDesc();

            $total+= $product->getUnitPrice();

            $data['results'][] = $item;
        }

        $data['total'] = number_format($total, 2, '.', '');

        return json_encode($data);
    }
}