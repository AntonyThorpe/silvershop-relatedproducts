<?php

namespace AntonyThorpe\SilverShopRelatedProducts;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\Forms\FieldList;
use SilverShop\Page\Product;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Forms\TextField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

/**
 * Can be applied to any Product to add the related product feature.
 *
 * @link https://github.com/dynamic/silverstripe-products/blob/4866c6a677d560fef4e7eee8b435f2b7533ff158/src/Extension/RelatedProductsDataExtension.php
 * @method ManyManyList<Product> RelatedProductsRelation()
 * @extends Extension<(Product & static)>
 */
class HasRelatedProducts extends Extension
{
    /**
     * @config
     */
    private static array $many_many = [
        'RelatedProductsRelation' => Product::class,
    ];

    /**
     * @config
     */
    private static array $many_many_extraFields = [
        'RelatedProductsRelation' => [
            'RelatedOrder' => 'Int',
            'RelatedTitle' => 'Varchar'
        ]
    ];

    public function updateCMSFields(FieldList $fieldList): void
    {
        if ($this->getOwner()->ID) {
            $fieldList->addFieldsToTab('Root.' . _t(self::class . '.Related', 'Related'), [
                $grid = GridField::create(
                    'RelatedProductsRelation',
                    _t(
                        self::class . '.RelatedProductsRelation',
                        'Related Products'
                    ),
                    $this->getOwner()->RelatedProductsRelation()->sort('RelatedOrder', 'ASC'),
                    $relatedConfig = GridFieldConfig_RelationEditor::create()
                        ->addComponent(GridFieldEditableColumns::create(), GridFieldEditButton::class)
                        ->removeComponentsByType([
                            GridFieldAddNewButton::class,
                            GridFieldEditButton::class,
                            GridFieldArchiveAction::class
                        ])
                )->setDescription(
                    _t(self::class . '.Description', 'Link related products using the search field top right and then add a title for this related product.  Drag and drop to reorder.')
                )
            ]);

            // Add RelatedTitle to GridField
            $columns = $grid->getConfig()->getComponentByType(GridFieldEditableColumns::class);
            if ($columns) {
                $columns->setDisplayFields([
                    'RelatedTitle' => fn($record, $column, $grid): TextField => TextField::create($column)
                ]);
            }

            // Format the autocomplete search for a product to link
            $autocompleter = $relatedConfig->getComponentByType(GridFieldAddExistingAutocompleter::class);
            if ($autocompleter) {
                $autocompleter->setSearchFields(['InternalItemID', 'Title'])
                    ->setResultsFormat('$InternalItemID - $Title');
            }

            // Add reorder capabilities when more than two items
            if ($this->getOwner()->RelatedProductsRelation()->count() > 1) {
                $relatedConfig->addComponent(GridFieldOrderableRows::create('RelatedOrder')->setRepublishLiveRecords(true));
            }
        }
    }

    public function getRelatedProducts(int $limit = 0, bool $random = false): ManyManyList
    {
        $related_products = $this->getOwner()->RelatedProductsRelation();

        $related_products = $random ? $related_products->sort("RAND()") : $related_products->sort('RelatedOrder');

        if ($limit !== 0) {
            $related_products = $related_products->limit($limit);
        }

        $this->getOwner()->extend('updateRelatedProducts', $related_products, $limit, $random);

        return $related_products;
    }

    /**
     * Cleanup
     */
    public function onBeforeDelete(): void
    {
        $this->getOwner()->RelatedProductsRelation()->removeAll();
    }
}
